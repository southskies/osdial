# 07/26/2013

ALTER TABLE osdial_campaigns ADD COLUMN ivr_id int(11) NOT NULL;##|##
 ## ;

UPDATE osdial_campaigns SET ivr_id=(SELECT id FROM osdial_ivr WHERE osdial_ivr.campaign_id=osdial_campaigns.campaign_id LIMIT 1);##|##
 ## ;

ALTER TABLE osdial_ivr DROP COLUMN campaign_id;##|##
 ## ;

CREATE TABLE osdial_extensions (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  ext_type enum('CUSTOM','DIALPLAN','IVR') NOT NULL DEFAULT 'CUSTOM',
  name varchar(50) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL DEFAULT '',
  ext_context varchar(255) NOT NULL DEFAULT 'osdial',
  exten varchar(255) NOT NULL DEFAULT '',
  ivr_id INT(11) NOT NULL,
  readonly enum('0','1') NOT NULL DEFAULT '0',
  selectable enum('0','1') NOT NULL DEFAULT '1'
) Engine=InnoDB;##|##
  ## ;

CREATE TABLE osdial_extensions_data (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  exten_id INT(11) UNSIGNED NOT NULL,
  ext_context varchar(255) NOT NULL DEFAULT 'osdial',
  exten varchar(255) NOT NULL DEFAULT '',
  ext_priority tinyint(3) unsigned NOT NULL DEFAULT '0',
  ext_app varchar(255) NOT NULL DEFAULT '',
  ext_appdata varchar(2047) NOT NULL DEFAULT '',
  KEY (`ext_context`,`exten`,`ext_priority`),
  KEY (`exten_id`)
) Engine=InnoDB;##|##
  ## ;


INSERT INTO `osdial_extensions` VALUES (1,'DIALPLAN','Say Goodbye','default audio for safe harbor 2-second-after-hello message then hangup','osdial','8307',0,'1','1');
INSERT INTO `osdial_extensions` VALUES (3,'DIALPLAN','Hangup','','osdial','8300',0,'1','1');
INSERT INTO `osdial_extensions` VALUES (4,'DIALPLAN','Play Ding','','osdial','8304',0,'1','1');
INSERT INTO `osdial_extensions` VALUES (5,'DIALPLAN','Detect VM, Sat Goodbye','this is used for playing a message to an answering machine forwarded from AMD in OSDIAL replace conf with the message file you want to leave','osdial','8320',0,'1','1');
INSERT INTO `osdial_extensions` VALUES (6,'DIALPLAN','VoiceMail Main Menu','Give voicemail at extension 8500','osdial','8500',0,'1','1');
INSERT INTO `osdial_extensions` VALUES (7,'CUSTOM','Echo Test','','osdial','9998',0,'1','1');
INSERT INTO `osdial_extensions` VALUES (8,'CUSTOM','Continuous Hold Music','','osdial','9999',0,'1','1');
INSERT INTO `osdial_extensions` VALUES (9,'DIALPLAN','DTMF for Conferences','this is used for sending DTMF signals within conference calls, the client app sends the digits to be played in the callerID field sound files must be placed in /var/lib/asterisk/sounds','osdial','8500998',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (10,'DIALPLAN','Direct to VoiceMain','this is used to allow the GUI to send you directly into voicemail don\'t forget to set GUI variable \\$voicemail_exten to this extension\\n','osdial','8501',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (11,'DIALPLAN','AD: Auto-agent script','8375: Auto-agent script.','osdial','8375',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (12,'DIALPLAN','AD: Load-Balance, plus AMD','8369: multi-server agent transfer, load-balance and plus AMD','osdial','8369',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (13,'DIALPLAN','Example CID by Areacode','use for selective CallerID hangup by area code(hard-coded)','osdial','8352',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (14,'DIALPLAN','AD: No-agent Campaign','8364: no agent campaign transfer','osdial','8364',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (15,'DIALPLAN','AD: Home Server Only','8365: single server agent transfer','osdial','8365',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (16,'DIALPLAN','AD: Single Server Example Survey','8366: old single server with initial survey','osdial','8266',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (17,'DIALPLAN','AD: Multi-Server, Load Sharing','8367: multi-server agent transfer, load-balance overflow','osdial','8367',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (18,'DIALPLAN','AD: Multi-Server, Load Balance','8368: multi-server agent transfer, load-balance','osdial','8368',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (19,'DIALPLAN','AD: Example reminder','8372: reminder script','osdial','8372',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (20,'DIALPLAN','AMD Backend','this is used for playing a message to an answering machine forwarded from AMD in OSDIAL replace conf with the message file you want to leave','osdial','8321',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (21,'DIALPLAN','Conf Recording WAV','this is used for recording conference calls, the client app sends the filename value as a callerID recordings go to /var/spool/asterisk/monitor (WAV)','osdial','8311',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (22,'DIALPLAN','Conf Recording GSM','this is used for recording conference calls, the client app sends the filename value as a callerID recordings go to /var/spool/asterisk/monitor (GSM)','osdial','8310',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (23,'DIALPLAN','Conf Recording WAV','this is used for recording conference calls, the client app sends the filename value as a callerID recordings go to /var/spool/asterisk/monitor (WAV)','osdial','8309',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (24,'DIALPLAN','Play Conf for Park','park channel for client GUI conferencing, hangup after 30 minutes','osdial','8302',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (25,'DIALPLAN','Park Customer','park channel for client GUI conferencing, hangup after 30 minutes','osdial','8303',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (26,'DIALPLAN','Park Customer','park channel for client GUI parking, hangup after 30 minutes','osdial','8301',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (27,'DIALPLAN','Dahdi Barge','barge monitoring extension','osdial','8159',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (28,'DIALPLAN','Record File WAV','prompt recording AGI script, ID is 4321','osdial','8167',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (29,'DIALPLAN','Record File GSM','prompt recording AGI script, ID is 4321','osdial','8168',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (30,'DIALPLAN','Record File ULAW','prompt recording AGI script, ID is 4321','osdial','8169',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (31,'DIALPLAN','Play Invalid','','osdial','9',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (32,'DIALPLAN','Echo','','osdial','43',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (33,'DIALPLAN','Timeout','','osdial','t',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (34,'DIALPLAN','Invalid Extension','','osdial','i',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (35,'DIALPLAN','Hangup Script','','osdial','h',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (36,'DIALPLAN','Invalid','','osdial','#',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (37,'DIALPLAN','VoiceMailMain','','osdial','*97',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (38,'DIALPLAN','VoiceMail Main Menu','','osdial','*98',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (39,'DIALPLAN','Forward to OSDial','','incoming','h',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (40,'DIALPLAN','Invalid Extension','','osdialBLOCK','#',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (41,'DIALPLAN','Invalid Extension','','osdialBLOCK','i',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (42,'DIALPLAN','Timeout','','osdialBLOCK','t',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (43,'DIALPLAN','Hangup Script','','osdialBLOCK','h',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (44,'DIALPLAN','Invalid','','osdialBLOCK','9',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (45,'DIALPLAN','Echo','','osdialBLOCK','43',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (46,'DIALPLAN','VoiceMailMain','','osdialBLOCK','*97',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (47,'DIALPLAN','VoiceMail Main Menu','','osdialBLOCK','*98',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (48,'DIALPLAN','DTMF for Conferences','','osdialBLOCK','8500998',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (49,'DIALPLAN','Echo Test','','osdialBLOCK','9998',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (50,'DIALPLAN','Continuous Hold Music','','osdialBLOCK','9999',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (51,'DIALPLAN','IVR Handling','','osdialBLOCK','487487',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (52,'DIALPLAN','IVR Handling','','osdialBLOCK','487488',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (53,'DIALPLAN','Testing','','osdialBLOCK','99999999999',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (54,'DIALPLAN','Testing','','osdialBLOCK','999999999999',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (55,'DIALPLAN','Start ARI VoiceMail','','osdial_arivmcall','s',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (56,'DIALPLAN','Hangup after answer','','osdial_arivmcall','s-ANSWER',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (57,'DIALPLAN','Repeat ARI VM Call','','osdial_arivmcall','5',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (58,'DIALPLAN','Disconnect','','osdial_arivmcall','#',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (59,'DIALPLAN','Main Menu','','osdial_arivmcall','*',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (60,'DIALPLAN','Invalid Extension','','osdial_arivmcall','i',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (61,'DIALPLAN','Timeout','','osdial_arivmcall','t',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (62,'DIALPLAN','Hangup','','osdial_arivmcall','h',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (63,'DIALPLAN','Agent Monitoring','6000 = monitoring, prompted\\n;   6+agent_exten = monitoring, direct','osdial','6000',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (64,'DIALPLAN','Agent Barge','7000 = barge, prompted\\n;   7+agent_exten = barge, direct','osdial','7000',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (65,'DIALPLAN','Agent Whisper','9000 = whisper, prompted\\n;   9+agent_exten = whisper, direct','osdial','9000',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (66,'DIALPLAN','Agent Monitoring','6000 = monitoring, prompted\\n;   6+agent_exten = monitoring, direct','osdialEXT','6000',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (67,'DIALPLAN','Agent Barge','7000 = barge, prompted\\n;   7+agent_exten = barge, direct','osdialEXT','7000',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (68,'DIALPLAN','Agent Whisper','9000 = whisper, prompted\\n;   9+agent_exten = whisper, direct','osdialEXT','9000',0,'1','0');
INSERT INTO `osdial_extensions` VALUES (69,'DIALPLAN','IVR Handling','','osdial','487488',0,'1','0');


INSERT INTO `osdial_extensions_data` VALUES (2,1,'osdial','8307',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (3,1,'osdial','8307',2,'Playback','vm-goodbye');
INSERT INTO `osdial_extensions_data` VALUES (4,1,'osdial','8307',3,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (5,3,'osdial','8300',1,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (6,4,'osdial','8304',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (7,4,'osdial','8304',2,'Playback','ding');
INSERT INTO `osdial_extensions_data` VALUES (8,4,'osdial','8304',3,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (9,5,'osdial','8320',1,'WaitForSilence','1000,2,20');
INSERT INTO `osdial_extensions_data` VALUES (10,5,'osdial','8320',2,'GotoIf','$[\"${AMDAUDIO}\" != \"\"]?4');
INSERT INTO `osdial_extensions_data` VALUES (11,5,'osdial','8320',3,'Set','AMDAUDIO=vm-goodbye');
INSERT INTO `osdial_extensions_data` VALUES (12,5,'osdial','8320',4,'Playback','${AMDAUDIO}');
INSERT INTO `osdial_extensions_data` VALUES (13,5,'osdial','8320',5,'Wait','4');
INSERT INTO `osdial_extensions_data` VALUES (14,5,'osdial','8320',6,'AGI','agi-OSDamd_post.agi,${EXTEN},${AMDAUDIO}');
INSERT INTO `osdial_extensions_data` VALUES (15,5,'osdial','8320',7,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (16,6,'osdial','8500',1,'VoiceMailMain','@osdial');
INSERT INTO `osdial_extensions_data` VALUES (17,6,'osdial','8500',2,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (18,9,'osdial','8500998',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (19,9,'osdial','8500998',2,'Playback','silence');
INSERT INTO `osdial_extensions_data` VALUES (20,9,'osdial','8500998',3,'AGI','agi-OSDdtmf.agi');
INSERT INTO `osdial_extensions_data` VALUES (21,9,'osdial','8500998',4,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (22,10,'osdial','8501',1,'VoiceMailMain','s${CALLERID(number)}@osdial');
INSERT INTO `osdial_extensions_data` VALUES (23,10,'osdial','8501',2,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (24,11,'osdial','8375',1,'NoOp','');
INSERT INTO `osdial_extensions_data` VALUES (25,11,'osdial','8375',2,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (26,11,'osdial','8375',3,'AGI','agi://127.0.0.1:4577/call_log');
INSERT INTO `osdial_extensions_data` VALUES (27,11,'osdial','8375',4,'AGI','agi-OSDamd.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (28,11,'osdial','8375',5,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (29,11,'osdial','8375',6,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (30,11,'osdial','8375',7,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (31,11,'osdial','8375',8,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (32,12,'osdial','8369',1,'NoOp','');
INSERT INTO `osdial_extensions_data` VALUES (33,12,'osdial','8369',2,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (34,12,'osdial','8369',3,'AGI','agi://127.0.0.1:4577/call_log');
INSERT INTO `osdial_extensions_data` VALUES (35,12,'osdial','8369',4,'AGI','agi-OSDamd.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (36,12,'osdial','8369',5,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (37,12,'osdial','8369',6,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (38,12,'osdial','8369',7,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (39,12,'osdial','8369',8,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (40,13,'osdial','8352',1,'AGI','agi-VDADselective_CID_hangup.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (41,13,'osdial','8352',2,'Playback','safe_harbor');
INSERT INTO `osdial_extensions_data` VALUES (42,13,'osdial','8352',3,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (43,14,'osdial','8364',1,'NoOp','');
INSERT INTO `osdial_extensions_data` VALUES (44,14,'osdial','8364',2,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (45,14,'osdial','8364',3,'AGI','agi://127.0.0.1:4577/call_log');
INSERT INTO `osdial_extensions_data` VALUES (46,14,'osdial','8364',4,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (47,14,'osdial','8364',5,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (48,14,'osdial','8364',6,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (49,14,'osdial','8364',7,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (50,15,'osdial','8365',1,'NoOp','');
INSERT INTO `osdial_extensions_data` VALUES (51,15,'osdial','8365',2,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (52,15,'osdial','8365',3,'AGI','agi://127.0.0.1:4577/call_log');
INSERT INTO `osdial_extensions_data` VALUES (53,15,'osdial','8365',4,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (54,15,'osdial','8365',5,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (55,15,'osdial','8365',6,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (56,15,'osdial','8365',7,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (57,16,'osdial','8266',1,'NoOp','');
INSERT INTO `osdial_extensions_data` VALUES (58,16,'osdial','8266',2,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (59,16,'osdial','8266',3,'AGI','agi://127.0.0.1:4577/call_log');
INSERT INTO `osdial_extensions_data` VALUES (60,16,'osdial','8266',4,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (61,16,'osdial','8266',5,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (62,16,'osdial','8266',6,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (63,16,'osdial','8266',7,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (64,17,'osdial','8367',1,'NoOp','');
INSERT INTO `osdial_extensions_data` VALUES (65,17,'osdial','8367',2,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (66,17,'osdial','8367',3,'AGI','agi://127.0.0.1:4577/call_log');
INSERT INTO `osdial_extensions_data` VALUES (67,17,'osdial','8367',4,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (68,17,'osdial','8367',5,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (69,17,'osdial','8367',6,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (70,17,'osdial','8367',7,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (71,18,'osdial','8368',1,'NoOp','');
INSERT INTO `osdial_extensions_data` VALUES (72,18,'osdial','8368',2,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (73,18,'osdial','8368',3,'AGI','agi://127.0.0.1:4577/call_log');
INSERT INTO `osdial_extensions_data` VALUES (74,18,'osdial','8368',4,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (75,18,'osdial','8368',5,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (76,18,'osdial','8368',6,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (77,18,'osdial','8368',7,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (78,19,'osdial','8372',1,'NoOp','');
INSERT INTO `osdial_extensions_data` VALUES (79,19,'osdial','8372',2,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (80,19,'osdial','8372',3,'AGI','agi://127.0.0.1:4577/call_log');
INSERT INTO `osdial_extensions_data` VALUES (81,19,'osdial','8372',4,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (82,19,'osdial','8372',5,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (83,19,'osdial','8372',6,'AGI','agi-OSDoutbound.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (84,19,'osdial','8372',7,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (85,20,'osdial','8321',1,'AGI','agi_OSDamd.agi,${EXTEN}-----YES');
INSERT INTO `osdial_extensions_data` VALUES (86,20,'osdial','8321',2,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (87,21,'osdial','8311',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (88,21,'osdial','8311',2,'MixMonitor','/var/spool/asterisk/record_cache/${CALLERID(name)}-in.wav,,/bin/mv -v ^{MIXMONITOR_FILENAME} /var/spool/asterisk/VDmonitor');
INSERT INTO `osdial_extensions_data` VALUES (89,21,'osdial','8311',3,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (90,21,'osdial','8311',4,'Wait','900');
INSERT INTO `osdial_extensions_data` VALUES (91,21,'osdial','8311',5,'Goto','osdial,8311,3');
INSERT INTO `osdial_extensions_data` VALUES (92,21,'osdial','8311',6,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (93,22,'osdial','8310',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (94,22,'osdial','8310',2,'MixMonitor','/var/spool/asterisk/record_cache/${CALLERID(name)}-in.gsm,,/bin/mv -v ^{MIXMONITOR_FILENAME} /var/spool/asterisk/VDmonitor');
INSERT INTO `osdial_extensions_data` VALUES (95,22,'osdial','8310',3,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (96,22,'osdial','8310',4,'Wait','900');
INSERT INTO `osdial_extensions_data` VALUES (97,22,'osdial','8310',5,'Goto','osdial,8310,3');
INSERT INTO `osdial_extensions_data` VALUES (98,22,'osdial','8310',6,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (99,23,'osdial','8309',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (100,23,'osdial','8309',2,'MixMonitor','/var/spool/asterisk/record_cache/${CALLERID(name)}-in.wav,,/bin/mv -v ^{MIXMONITOR_FILENAME} /var/spool/asterisk/VDmonitor');
INSERT INTO `osdial_extensions_data` VALUES (101,23,'osdial','8309',3,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (102,23,'osdial','8309',4,'Wait','900');
INSERT INTO `osdial_extensions_data` VALUES (103,23,'osdial','8309',5,'Goto','osdial,8309,3');
INSERT INTO `osdial_extensions_data` VALUES (104,23,'osdial','8309',6,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (105,24,'osdial','8302',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (106,24,'osdial','8302',2,'Playback','conf');
INSERT INTO `osdial_extensions_data` VALUES (107,24,'osdial','8302',3,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (108,25,'osdial','8303',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (109,25,'osdial','8303',2,'AGI','agi-OSDpark.agi');
INSERT INTO `osdial_extensions_data` VALUES (110,25,'osdial','8303',3,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (111,26,'osdial','8301',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (112,26,'osdial','8301',2,'AGI','agi-OSDpark.agi');
INSERT INTO `osdial_extensions_data` VALUES (113,26,'osdial','8301',3,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (114,27,'osdial','8159',1,'DahdiBarge','');
INSERT INTO `osdial_extensions_data` VALUES (115,27,'osdial','8159',2,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (116,28,'osdial','8167',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (117,28,'osdial','8167',2,'AGI','agi-record_prompts.agi,wav,720000');
INSERT INTO `osdial_extensions_data` VALUES (118,28,'osdial','8167',3,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (119,29,'osdial','8168',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (120,29,'osdial','8168',2,'AGI','agi-record_prompts.agi,gsm,720000');
INSERT INTO `osdial_extensions_data` VALUES (121,29,'osdial','8168',3,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (122,30,'osdial','8169',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (123,30,'osdial','8169',2,'AGI','agi-record_prompts.agi,ulaw,720000');
INSERT INTO `osdial_extensions_data` VALUES (124,30,'osdial','8169',3,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (125,31,'osdial','9',1,'Playback','invalid');
INSERT INTO `osdial_extensions_data` VALUES (126,32,'osdial','43',1,'Echo','');
INSERT INTO `osdial_extensions_data` VALUES (127,33,'osdial','t',1,'Goto','osdial,#,1');
INSERT INTO `osdial_extensions_data` VALUES (128,34,'osdial','i',1,'Playback','invalid');
INSERT INTO `osdial_extensions_data` VALUES (129,35,'osdial','h',1,'AGI','agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----${HANGUPCAUSE}-----${DIALSTATUS}-----${DIALEDTIME}-----${ANSWEREDTIME}');
INSERT INTO `osdial_extensions_data` VALUES (130,36,'osdial','#',1,'Playback','invalid');
INSERT INTO `osdial_extensions_data` VALUES (131,36,'osdial','#',2,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (132,37,'osdial','*97',1,'Goto','osdial,8501,1');
INSERT INTO `osdial_extensions_data` VALUES (133,38,'osdial','*98',1,'Goto','osdial,8500,1');
INSERT INTO `osdial_extensions_data` VALUES (134,39,'incoming','h',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (135,40,'osdialBLOCK','#',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (136,42,'osdialBLOCK','t',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (137,43,'osdialBLOCK','h',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (138,44,'osdialBLOCK','9',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (139,45,'osdialBLOCK','43',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (140,46,'osdialBLOCK','*97',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (141,47,'osdialBLOCK','*98',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (142,48,'osdialBLOCK','8500998',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (143,49,'osdialBLOCK','9998',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (144,50,'osdialBLOCK','9999',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (145,51,'osdialBLOCK','487487',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (146,52,'osdialBLOCK','487488',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (147,53,'osdialBLOCK','99999999999',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (148,54,'osdialBLOCK','999999999999',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (149,55,'osdial_arivmcall','s',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (150,55,'osdial_arivmcall','s',2,'Wait','1');
INSERT INTO `osdial_extensions_data` VALUES (151,55,'osdial_arivmcall','s',3,'Background','${MSG}');
INSERT INTO `osdial_extensions_data` VALUES (152,55,'osdial_arivmcall','s',4,'Background','silence/2');
INSERT INTO `osdial_extensions_data` VALUES (153,55,'osdial_arivmcall','s',5,'Background','vm-repeat');
INSERT INTO `osdial_extensions_data` VALUES (154,55,'osdial_arivmcall','s',6,'Background','vm-starmain');
INSERT INTO `osdial_extensions_data` VALUES (155,55,'osdial_arivmcall','s',7,'WaitExten','15');
INSERT INTO `osdial_extensions_data` VALUES (156,56,'osdial_arivmcall','s-ANSWER',1,'NoOp','Call successfully answered - Hanging up now');
INSERT INTO `osdial_extensions_data` VALUES (157,57,'osdial_arivmcall','5',1,'Goto','osdial_arivmcall,s,3');
INSERT INTO `osdial_extensions_data` VALUES (158,58,'osdial_arivmcall','#',1,'Playback','vm-goodbye');
INSERT INTO `osdial_extensions_data` VALUES (159,58,'osdial_arivmcall','#',2,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (160,59,'osdial_arivmcall','*',1,'Set','VMCONTEXT=${DB(AMPUSER/${MBOX}/voicemail)}');
INSERT INTO `osdial_extensions_data` VALUES (161,59,'osdial_arivmcall','*',2,'VoiceMailMain','${MBOX}@${VMCONTEXT},s');
INSERT INTO `osdial_extensions_data` VALUES (162,60,'osdial_arivmcall','i',1,'Playback','pm-invalid-option');
INSERT INTO `osdial_extensions_data` VALUES (163,60,'osdial_arivmcall','i',2,'Goto','osdial_arivmcall,s,3');
INSERT INTO `osdial_extensions_data` VALUES (164,61,'osdial_arivmcall','t',1,'Playback','vm-goodbye');
INSERT INTO `osdial_extensions_data` VALUES (165,61,'osdial_arivmcall','t',2,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (166,62,'osdial_arivmcall','h',1,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (167,63,'osdial','6000',1,'AGI','agi-OSDstation_spy_prompted.agi');
INSERT INTO `osdial_extensions_data` VALUES (168,64,'osdial','7000',1,'AGI','agi-OSDstation_spy_prompted.agi');
INSERT INTO `osdial_extensions_data` VALUES (169,65,'osdial','9000',1,'AGI','agi-OSDstation_spy_prompted.agi');
INSERT INTO `osdial_extensions_data` VALUES (170,66,'osdialEXT','6000',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (171,67,'osdialEXT','7000',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (172,68,'osdialEXT','9000',1,'Goto','osdial,${EXTEN},1');
INSERT INTO `osdial_extensions_data` VALUES (173,69,'osdial','487488',1,'Answer','');
INSERT INTO `osdial_extensions_data` VALUES (174,69,'osdial','487488',2,'Playback','sip-silence');
INSERT INTO `osdial_extensions_data` VALUES (175,69,'osdial','487488',3,'AGI','agi-OSDivr.agi,${EXTEN}');
INSERT INTO `osdial_extensions_data` VALUES (176,69,'osdial','487488',4,'Hangup','');
INSERT INTO `osdial_extensions_data` VALUES (177,41,'osdialBLOCK','i',1,'Goto','osdial,${EXTEN},1');


UPDATE system_settings SET version='3.0.1.105',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.105 and clearing last_update_check flag.;
