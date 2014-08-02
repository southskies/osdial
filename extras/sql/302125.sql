# 07/31/2014

ALTER TABLE osdial_campaigns MODIFY `submit_method` enum('NORMAL','WEBFORM1','WEBFORM2','PASSBACK1','PASSBACK2') DEFAULT 'NORMAL';##|##
 ## Add option to wait for the disposition to be sent back to us.;

INSERT INTO `osdial_extensions` (`ext_type`,`name`,`description`,`ext_context`,`exten`,`ivr_id`,`readonly`,`selectable`) VALUES ('DIALPLAN','Detect VM, Send to Ext','this is used for playing a message to an answering machine forwarded from AMD in OSDIAL replace with the extension to use','osdial','8319',0,'1','1');##|##
 ## Add exten 8319, Detect VM, Send to Ext.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8319'),'osdial','8319',1,'WaitForSilence','1000,2,20');##|##
 ## Add exten 8319, Detect VM, Send to Ext.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8319'),'osdial','8319',2,'GotoIf','$[\"${AMDEXTEN}\" != \"\"]?4');##|##
 ## Add exten 8319, Detect VM, Send to Ext.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8319'),'osdial','8319',3,'Set','AMDEXTEN=8307');##|##
 ## Add exten 8319, Detect VM, Send to Ext.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8319'),'osdial','8319',4,'Dial','Local/${AMDEXTEN}@osdial,,g');##|##
 ## Add exten 8319, Detect VM, Send to Ext.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8319'),'osdial','8319',5,'Wait','1');##|##
 ## Add exten 8319, Detect VM, Send to Ext.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8319'),'osdial','8319',6,'AGI','agi-OSDamd_post.agi,${EXTEN},${AMDEXTEN}');##|##
 ## Add exten 8319, Detect VM, Send to Ext.;

INSERT INTO `osdial_extensions_data` (`exten_id`,`ext_context`,`exten`,`ext_priority`,`ext_app`,`ext_appdata`) VALUES ((SELECT `id` FROM `osdial_extensions` WHERE `ext_context`='osdial' AND `exten`='8319'),'osdial','8319',7,'Hangup','');##|##
 ## Add exten 8319, Detect VM, Send to Ext.;

UPDATE system_settings SET version='3.0.2.125',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.2.125 and clearing last_update_check flag.;
