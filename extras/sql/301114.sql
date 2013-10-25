# 10/24/2013

UPDATE osdial_extensions_data SET ext_appdata='silence&ding' WHERE ext_context='osdial' AND exten='8304' and ext_app='Playback' AND ext_appdata='ding';##|##
 ## Play silence before short ding sound.;

INSERT INTO `osdial_extensions` (`ext_type`,`name`,`description`,`ext_context`,`exten`,`ivr_id`,`readonly`,`selectable`) VALUES ('DIALPLAN','Play Conf Enter','','osdial','8305',0,'1','1');##|##
 ## Add exten 8305, Play conf enter sound.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8305'),'osdial','8305',1,'Answer','');##|##
 ## Add exten 8305, Play conf enter sound.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8305'),'osdial','8305',2,'Playback','silence&enter');##|##
 ## Add exten 8305, Play conf enter sound.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8305'),'osdial','8305',3,'Hangup','');##|##
 ## Add exten 8305, Play conf enter sound.;

INSERT INTO `osdial_extensions` (`ext_type`,`name`,`description`,`ext_context`,`exten`,`ivr_id`,`readonly`,`selectable`) VALUES ('DIALPLAN','Play Conf Leave','','osdial','8306',0,'1','1');##|##
 ## Add exten 8306, Play conf leave sound.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8306'),'osdial','8306',1,'Answer','');##|##
 ## Add exten 8306, Play conf leave sound.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8306'),'osdial','8306',2,'Playback','silence&leave');##|##
 ## Add exten 8306, Play conf leave sound.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8306'),'osdial','8306',3,'Hangup','');##|##
 ## Add exten 8306, Play conf leave sound.;

ALTER TABLE osdial_inbound_groups MODIFY `agent_alert_exten` varchar(20) DEFAULT '8305';##|##
 ## Change default agent_alert_exten to 8305.;

UPDATE osdial_inbound_groups SET `agent_alert_exten`='8305' WHERE `agent_alert_exten`='8304';##|##
 ## Change any ingroup with an agent_alert_exten of 8304 to 8305.;

INSERT INTO `osdial_extensions` (`ext_type`,`name`,`description`,`ext_context`,`exten`,`ivr_id`,`readonly`,`selectable`) VALUES ('DIALPLAN','InGroup Hangup Handler','','osdial','8330',0,'1','1');##|##
 ## Add exten 8330, InGroup Hangup Handler.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8330'),'osdial','8330',1,'Originate','Local/9${LASTAGENTCONF}@osdial,exten,osdial,${CHANALERTLEAVE},1');##|##
 ## Add exten 8330, InGroup Hangup Handler.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8330'),'osdial','8330',2,'Return','');##|##
 ## Add exten 8330, InGroup Hangup Handler.;

UPDATE system_settings SET version='3.0.1.114',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.114 and clearing last_update_check flag.;
