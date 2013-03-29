<?php
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
# Copyright (C) 2009  Steve Szmidt <techs@callcentersg.com>  LICENSE: AGPLv3
#
#     This file is part of OSDial.
#
#     OSDial is free software: you can redistribute it and/or modify
#     it under the terms of the GNU Affero General Public License as
#     published by the Free Software Foundation, either version 3 of
#     the License, or (at your option) any later version.
#
#     OSDial is distributed in the hope that it will be useful,
#     but WITHOUT ANY WARRANTY; without even the implied warranty of
#     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#     GNU Affero General Public License for more details.
#
#     You should have received a copy of the GNU Affero General Public
#     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
#
# 0906090157 - Added XFER_INGROUP forms.

if ($campaign_id != '') {
    $oivr = get_first_record($link, 'osdial_ivr', '*', sprintf("campaign_id='%s'",mres($campaign_id)));
    if ($oivr['id'] != '') {
        $oivr_id = $oivr['id'];
    } else {
        $SUB = "";
        $ADD = "1menu"; # go to campaign modification form below
    }
}

#$ivrpath = $WeBServeRRooT . "/ivr";
$ivrpath = "/opt/osdial/media";


######################
# ADD=1menu create new menu
######################
if ($ADD == "1menu") {
    if ($LOG['modify_campaigns'] == 1) {
        $oivr_id = 0;
        $oivr = get_first_record($link, 'osdial_ivr', '*', sprintf("campaign_id='%s'",mres($campaign_id)));
        if ($oivr['campaign_id'] != "") {
            $SUB = "2keys";
            $ADD = "3menu"; # go to campaign modification form below
        } else {
            $gfr = get_first_record($link, 'osdial_campaigns', '*', sprintf("campaign_id='%s'",mres($campaign_id)));
            echo "<br><B><font color=$default_text>IVR CREATED</font></B>\n";
            $stmt=sprintf("INSERT INTO osdial_ivr (campaign_id,name) VALUES ('%s','%s');",mres($campaign_id),mres($gfr['campaign_name']));
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|CREATE OIVR |$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
            $oivr = get_first_record($link, 'osdial_ivr', '*', sprintf("campaign_id='%s'",mres($campaign_id)));
            $id = $oivr['id'];
            $oivr_id = $oivr['id'];
            $SUB = "2keys";
            $ADD = "3menu"; # go to campaign modification form below
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

# Format key updates, ie action_data.
if ($ADD == "1keys" or $ADD == '4keys') {
	if ($oivr_opt_action == 'XFER_INGROUP' or $oivr_opt_action == 'HANGUP' or $oivr_opt_action == 'PLAYFILE' or $oivr_opt_action == 'XFER_EXTERNAL' or $oivr_opt_action == 'XFER_EXTERNAL_MULTI' or $oivr_opt_action == 'MENU') {
            # Upload recording
            $recfile = $_FILES['recfile'];
            $recfiletmp = $_FILES['recfile']['tmp_name'];
            $recfilename = $_FILES['recfile']['name'];
            $recfilename = OSDpreg_replace('/ /','_',$recfilename);
            $recfilename = OSDpreg_replace('/[^-\_\.0-9a-zA-Z]/',"",$recfilename);
            $recfilename = OSDpreg_replace('/\.wav$/i','.wav',$recfilename);
            $recfilename = OSDpreg_replace('/\.gsm$/i','.gsm',$recfilename);
            $recfilename = OSDpreg_replace('/\.mp3$/i','.mp3',$recfilename);
            if ($recfilename != '') {
                rename($recfiletmp, '/tmp/'.$recfilename);
                media_add_file($link, '/tmp/'.$recfilename, mimemap($recfilename), "IVR: $campaign_id - $oivr_opt_action",'',1);
                copy('/tmp/'.$recfilename, $WeBServeRRooT . "/ivr/" . $recfilename);
                unlink('/tmp/'.$recfilename);
                if ($oivr_opt_action == 'MENU') {
                    $oi3 = $recfilename;
                } else {
                    $oi1 = $recfilename;
                }
            }
	}
	if ($oivr_opt_action == 'MENU_REPEAT' or $oivr_opt_action == 'MENU_EXIT') {
		$d_ary = array($oi1);
	} elseif ($oivr_opt_action == 'AGENT_EXTENSIONS') {
        $oivr_opt_keypress='A';
        $chks = get_variable('chks');
        $oi3='';
        foreach ($chks as $chk) {
            $ext = get_variable('ext'.$chk);
            $oi3.=$chk.':'.$ext.'|';
        }
        $oi3=rtrim($oi3,'|');
		$d_ary = array($oi1,$oi2,$oi3);
	} elseif ($oivr_opt_action == 'HANGUP') {
		$d_ary = array($oi1,$oi2);
	} elseif (OSDpreg_match('/^PLAYFILE/',$oivr_opt_action) or $oivr_opt_action == 'XFER_EXTERNAL') {
		$d_ary = array($oi1,$oi2,$oi3);
	} elseif ($oivr_opt_action == 'XFER_EXTERNAL_MULTI') {
		$oi4 = OSDpreg_replace("/\r/","",$oi4);
		$oi4 = OSDpreg_replace("/\n/","#:#",$oi4);
		$d_ary = array($oi1,$oi2,$oi3,$oi4);
	} elseif ($oivr_opt_action == 'XFER_INGROUP') {
		$d_ary = array($oi1,$oi2,$oi3,$oi4,$oi5);
	} elseif ($oivr_opt_action == 'TVC_LOOKUP') {
		$d_ary = array($oi1,$oi2,$oi3,$oi4,$oi5,$oi6,$oi7,$oi8,$oi9,$oi10,$oi11);
	} else {
		$d_ary = array($oi1,$oi2,$oi3,$oi4,$oi5,$oi6,$oi7,$oi8,$oi9);
	}
	$oivr_opt_action_data = implode('#:#',$d_ary);
}

######################
# ADD=1keys create new key
######################
if ($ADD == "1keys") {
    if ($LOG['modify_campaigns'] == 1) {
        if (($oivr_id == 0) or (OSDstrlen($oivr_opt_keypress) < 1) or (OSDstrlen($oivr_opt_action) < 1) or (OSDstrlen($oivr_opt_action_data) < 1)) {
            echo "<br><font color=red>KEY NOT CREATED - Please go back and look at the data you entered\n";
            $ADD = "2keys";
        } else {
            echo "<br><B><font color=$default_text>KEY CREATED: $oivr_id - $oivr_opt_action - $oivr_opt_keypress</font></B>\n";
            $stmt=sprintf("INSERT INTO osdial_ivr_options (ivr_id,parent_id,keypress,action,action_data) VALUES ('%s','%s','%s','%s','%s');",mres($oivr_id),mres($oivr_opt_parent_id),mres($oivr_opt_keypress),mres($oivr_opt_action),mres($oivr_opt_action_data));
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|CREATE OIVRO |$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
            $id = $form_id;
            $SUB = "2keys";
            $ADD = "3menu"; # go to campaign modification form below
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=2keys add a new key
######################
if ($ADD == "2keys" and ($oivr_opt_keypress == '' or $oivr_opt_action == '')) {
    echo "<center><br><font color=$default_text size=+1>YOU MUST SELECT A KEYPRESS AND AN ACTION</font><br><br>\n";
    $ADD = '3menu';
    $SUB = '2keys';
}
if ($ADD == "2keys") {
    echo "<center><br><font color=$default_text size=+1>NEW KEYPRESS ACTION</font><br><br>\n";

    echo '<form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
    echo '<input type="hidden" name="ADD" value="1keys">';
    echo '<input type="hidden" name="oivr_id" value="' . $oivr_id . '">';
    echo '<input type="hidden" name="oivr_opt_parent_id" value="' . $oivr_opt_parent_id . '">';
    echo '<input type="hidden" name="campaign_id" value="' . $campaign_id . '">';
    echo '<input type="hidden" name="oivr_opt_action" value="' . $oivr_opt_action . '">';
    echo '<input type="hidden" name="oivr_opt_keypress" value="' . $oivr_opt_keypress . '">';

    echo "<table cellspacing=1 cellpadding=5>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Campaign/IVR:</td>\n";
    echo '      <td bgcolor="' . $oddrows . '">' . $campaign_id . '/' . $oivr_id . '</td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Key:</td>\n";
    echo '      <td bgcolor="' . $oddrows . '">' . $oivr_opt_keypress . '</td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Action:</td>\n";
    echo '      <td bgcolor="' . $oddrows . '">' . $oivr_opt_action . '</td>';
    echo "  </tr>\n";



    $o = $oivr_opt_action;
    if ($o == 'PLAYFILE') {
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi1', '', 20, 50);
        #echo media_file_text_options($link, 'oi1', '', 20, 50);
    	#echo '          <select name="oi1">';
        #echo media_file_select_options($link);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            if ($stat['status'] == 'VPLAY') {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo '<input type="hidden" name="oi3" value="1">';
    } elseif ($o == 'PLAYFILE_FIELD') { 
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows align=right>Field to Use</td>\n";
        echo '    <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi1">';
        echo '        <option value="custom1">custom1</option>';
        echo '        <option value="custom2">custom2</option>';
        echo "      </select>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '    <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            if ($stat['status'] == 'VPLAY') {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows align=right>Hangup After?</td>\n";
        echo '    <td bgcolor="' . $oddrows . '">';
        echo "      <input type=\"checkbox\" name=\"oi3\" value=\"Y\">\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    } elseif ($o == 'HANGUP') {
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Hangup (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi1', '', 20, 50);
        #echo media_file_text_options($link, 'oi1', '', 20, 50);
    	#echo '          <select name="oi1">';
        #echo media_file_select_options($link);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            if ($stat['status'] == 'VNI') {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
    } elseif ($o == 'MENU') { 
        echo '<input type="hidden" name="oi1" value="' . $oivr_id . '">';
        echo '<input type="hidden" name="oi2" value="' . $oivr_opt_parent_id . '">';
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Announcement Recording</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi3', '', 20, 50);
        #echo media_file_text_options($link, 'oi3', '', 20, 50);
    	#echo '          <select name="oi3">';
        #echo media_file_select_options($link);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Announcement Repeat Attempts</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="3" maxlength="2" name="oi4" value="3"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Wait for Key Attempts</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="3" maxlength="2" name="oi5" value="5"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Wait for Key Timeout (ms)</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="5" maxlength="4" name="oi6" value="500"><input type="hidden" name="oi7" value=""><input type="hidden" name="oi8" value=""></td>';
        echo "  </tr>\n";
    } elseif ($o == 'MENU_REPEAT') { 
        echo '<input type="hidden" name="oi1" value="1">';
    } elseif ($o == 'MENU_EXIT') { 
        echo '<input type="hidden" name="oi1" value="1">';
    } elseif ($o == 'AGENT_EXTENSIONS') { 
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '    <td bgcolor="' . $oddrows . '">';
        echo '      <input type="hidden" name="oi1" value="' . $oivr_id . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            if ($stat['status'] == 'VIXFER') {
                $sel = ' selected';
            }
            echo "        <option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "      </select>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows colspan=2 align=center><i>Note: Agents must have the Agent2Agent option enabled under their user profile in order to receive calls from the IVR.</i></td>\n";
        echo "  </tr>\n";

        $asel = array();
        foreach (explode('|',$ad[2]) as $apck) {
            $a1 = explode(':',$apck);
            if ($a1[0]!='') {
                $asel[$a1[0]]=$a1[1];
            }
        }
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows colspan=2 align=center>\n";
        echo "      <table bgcolor=grey cellpadding=0 cellspacing=1>\n";
        echo "        <tr class=tabheader>\n";
        echo "          <td>&nbsp</td>\n";
        echo "          <td>AGENT</td>\n";
        echo "          <td>EXTENSION</td>\n";
        echo "        </tr>\n";
        $camppre=''; if ($LOG['multicomp']) $camppre = OSDsubstr($campaign_id,0,3);
        $agents = get_krh($link, 'osdial_users', 'user,full_name','',sprintf("user_level>3 AND xfer_agent2agent='1' AND user LIKE '%s%%'",mres($camppre)),'');
        foreach ($agents as $agent) {
            echo "        <tr>\n";
            $achk=''; if (isset($asel[$agent['user']])) $achk='checked';
            echo '          <td bgcolor=' . $oddrows . '><input type=checkbox name=chks[] ' . $achk . ' value="' . $agent['user'] . '"></td>' . "\n";
            echo '          <td bgcolor=' . $oddrows . '><span class=font2>' . $agent['user'] . ': ' . $agent['full_name'] . '</span></td>' . "\n";
            if (!isset($asel[$agent['user']])) $asel[$agent['user']] = $agent['user'];
            echo '          <td bgcolor=' . $oddrows . '><input type=text name="ext' . $agent['user'] . '" size=10 value="' . $asel[$agent['user']] . '"></td>' . "\n";
            echo "        </tr>\n";
        }
        echo "        <tr class=tabfooter>\n";
        echo "          <td colspan=3></td>\n";
        echo "        </tr>\n";
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    } elseif ($o == 'TVC_LOOKUP') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Description</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi1" value=""></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            if ($stat['status'] == 'VIXFER') {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Phone# Prompt File</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi3">';
        echo media_file_select_options($link);
    	echo "          </select>";
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Agent# Prompt File</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi4">';
        echo media_file_select_options($link);
    	echo "          </select>";
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>In-Group to transfer to</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi5"><option value="">-NONE-</option>';
        $ingroups = get_krh($link, 'osdial_inbound_groups', 'group_id,group_name','',"active='Y'",'');
        foreach ($ingroups as $ing) {
            echo "<option value=\"" . $ing['group_id'] . "\">" . $ing['group_id'] . " : " . $ing['group_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>MySQL Server:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi6" value=""></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>MySQL Database:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi7" value=""></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>MySQL User:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi8" value=""></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>MySQL Password:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi9" value=""></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Table:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi10" value=""></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Field Mappings:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="1000" name="oi11" value=""><br><font size=-1>Format (use pipe to concat): phone_number=dbfld1,first_name=fname,comments=dbfld2|dbfld2|dbfld3</font></td>';
        echo "  </tr>\n";
    } elseif ($o == 'XFER_INGROUP') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi1', '', 20, 50);
        #echo media_file_text_options($link, 'oi1', '', 20, 50);
    	#echo '          <select name="oi1">';
        #echo media_file_select_options($link);
    	#echo "          </select>\n";
        echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            if ($stat['status'] == 'VIXFER') {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>In-Group to transfer to</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi3"><option value="">-NONE-</option>';
        $ingroups = get_krh($link, 'osdial_inbound_groups', 'group_id,group_name','',"active='Y'",'');
        foreach ($ingroups as $ing) {
            echo "<option value=\"" . $ing['group_id'] . "\">" . $ing['group_id'] . " : " . $ing['group_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Failover Method</td>";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '          <select name="oi4">';
        echo '              <option value="">-NONE-</option>';
        echo '              <option value="EXT_NA">XFer to ext if no agents logged in.</option>';
        echo '              <option value="EXT_UA">XFer to ext if agents are unavailable.</option>';
        echo '          </select>';
        echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Extension/Number to Transfer Call to:</td>";
        echo '      <td bgcolor="' . $oddrows . '">';
        #echo '<input type="text" size="12" maxlength="10" name="oi5" value="">';
        echo phone_extension_text_options($link, 'oi5', '', 15, 30);
        echo '</td>';
        echo "  </tr>\n";
    } elseif ($o == 'XFER_EXTERNAL') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi1', '', 20, 50);
        #echo media_file_text_options($link, 'oi1', '', 20, 50);
    	#echo '          <select name="oi1">';
        #echo media_file_select_options($link);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel = '';
            if ($stat['status'] == 'VEXFER') {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Extension/Number to Transfer Call to:<br>";
        echo "      Format: 9995551212</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        #echo '<input type="text" size="12" maxlength="10" name="oi3" value="">';
        echo phone_extension_text_options($link, 'oi3', '', 20, 30);
        echo '</td>';
        echo "  </tr>\n";
    } elseif ($o == 'XFER_EXTERNAL_MULTI') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi1', '', 20, 50);
        #echo media_file_text_options($link, 'oi1', '', 20, 50);
    	#echo '          <select name="oi1">';
        #echo media_file_select_options($link);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel = '';
            if ($stat['status'] == 'VEXFER') {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Phone Number Order</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi3"><option value="ROUNDROBIN" selected>Round-Robin</option>';
        echo '      <option value="RANDOM">Random</option></select>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Extensions/Numbers to Transfer Call to:\n";
        echo "      Format: 9995551212</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><textarea name="oi4" cols=10 rows=20></textarea></td>';
        echo "  </tr>\n";
    }



    echo "  <tr><td colspan=2 bgcolor=$oddrows>&nbsp;</td></tr>\n";
    echo "  <tr class=tabfooter>\n";
    echo "      <td colspan=2 class=tabbutton align=center><input type=submit value=\"Create Key Entry\"></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";

    echo "</form>";
}




######################
# ADD=4menu modify menu
######################
if ($ADD == "4menu") {
    if ($LOG['modify_campaigns'] == 1) {
        if (($oivr_id < 1) or ($oivr_repeat_loops < 1) or ($oivr_wait_loops < 1) or ($oivr_wait_timeout < 1)) {
            echo "<br><font color=red>IVR NOT MODIFIED - Please go back and look at the data you entered\n";
        } else {
            # Upload recording
            $recfile = $_FILES['recfile'];
            $recfiletmp = $_FILES['recfile']['tmp_name'];
            $recfilename = $_FILES['recfile']['name'];
            $recfilename = OSDpreg_replace('/ /','_',$recfilename);
            $recfilename = OSDpreg_replace('/[^-\_\.0-9a-zA-Z]/',"",$recfilename);
            $recfilename = OSDpreg_replace('/\.wav$/i','.wav',$recfilename);
            $recfilename = OSDpreg_replace('/\.gsm$/i','.gsm',$recfilename);
            $recfilename = OSDpreg_replace('/\.mp3$/i','.mp3',$recfilename);
            if ($recfilename != '') {
                rename($recfiletmp, '/tmp/'.$recfilename);
                media_add_file($link, '/tmp/'.$recfilename, mimemap($recfilename), "IVR: $campaign_id - MAIN_MENU",'',1);
                copy('/tmp/'.$recfilename, $WeBServeRRooT . "/ivr/" . $recfilename);
                unlink('/tmp/'.$recfilename);
                $oivr_announcement = $recfilename;
            }

            if ($status == 'ACTIVE' and $oivr_virtual_agents == '') {
                $oivr_virtual_agents='1';
            }
            if ($status == 'ACTIVE' and $oivr_reserve_agents == '') {
                $oivr_reserve_agents='2';
            }

            if ($oivr_allow_inbound == '') {
                $oivr_allow_inbound = 'Y';
            }

            if ($oivr_name == '') {
                $gfr = get_first_record($link, 'osdial_campaigns', 'campaign_name',sprintf("campaign_id='%s'",mres($campaign_id)));
                $oivr_name = $gfr['campaign_name'];
            }

            if ($oivr_allow_agent_extensions == '') $oivr_allow_agent_extensions = 'N';
            $gfr = get_first_record($link, 'osdial_ivr_options', 'count(*) AS count',sprintf("ivr_id='%s' AND parent_id='0' AND keypress='A'",mres($oivr_id)));
            if ($oivr_allow_agent_extensions == 'Y') {
                if ($gfr['count']==0) {
                    $stmt=sprintf("INSERT INTO osdial_ivr_options (ivr_id,parent_id,keypress,action,action_data) VALUES ('%s','0','A','AGENT_EXTENSIONS','Agent Extensions#:#VIXFER');",mres($oivr_id));
                    $rslt = mysql_query($stmt, $link);
                }
            } else {
                if ($gfr['count']>0) {
                    $stmt=sprintf("DELETE FROM osdial_ivr_options WHERE ivr_id='%s' AND parent_id='0' AND keypress='A';",mres($oivr_id));
                    $rslt = mysql_query($stmt, $link);
                }
            }

            echo "<br><B><font color=$default_text>IVR MODIFIED: $oivr_id - $campaign_id - $oivr_name</font></B>\n";
            $stmt=sprintf("UPDATE osdial_ivr SET name='%s',announcement='%s',repeat_loops='%s',wait_loops='%s',wait_timeout='%s',answered_status='%s',virtual_agents='%s',status='%s',timeout_action='%s',reserve_agents='%s',allow_inbound='%s',allow_agent_extensions='%s' WHERE id='%s';",mres($oivr_name),mres($oivr_announcement),mres($oivr_repeat_loops),mres($oivr_wait_loops),mres($oivr_wait_timeout),mres($oivr_answered_status),mres($oivr_virtual_agents),mres($oivr_status),mres($oivr_timeout_action),mres($oivr_reserve_agents),mres($oivr_allow_inbound),mres($oivr_allow_agent_extensions),mres($oivr_id));
            $rslt = mysql_query($stmt, $link);


            $svrs = get_krh($link, 'servers', 'server_ip','',"server_profile IN ('AIO','DIALER') AND active='Y'",'');
            # Insert Virtual Agents.
            $rma = get_krh($link, 'osdial_remote_agents', 'remote_agent_id,user_start','',sprintf("user_start LIKE 'va%s%%'",mres($campaign_id)),'');
            $rcnt = count($rma);
            if ($rcnt < ($oivr_virtual_agents + $oivr_reserve_agents)) {
                $icnt = 0;
                $unum = 0;
                while ($icnt < (($oivr_virtual_agents + $oivr_reserve_agents) - $rcnt)) {
                    $unum++;
                    $usr = 'va' . $campaign_id . sprintf('%03d', $unum);
                    $ufnd = 0;
                    foreach ($rma as $ru) {
                        if ($ru['user_start'] == $usr) {
                            $ufnd++;
                        }
                    }
                    if ($ufnd == 0) {
                        $stmt=sprintf("DELETE FROM osdial_remote_agents WHERE user_start='%s';",mres($usr));
                        $rslt = mysql_query($stmt, $link);
                        $stmt=sprintf("DELETE FROM osdial_live_agents WHERE user='%s';",mres($usr));
                        $rslt = mysql_query($stmt, $link);
                        $server_ip = $svrs[array_rand($svrs)]['server_ip'];
                        $stmt = "INSERT INTO osdial_remote_agents (user_start,conf_exten,server_ip,campaign_id) VALUES ";
                        $conf = '87' . sprintf('%03d',$oivr_id) . sprintf('%03d',$unum);
                        $stmt .= sprintf("('%s','%s','%s','%s');",mres($usr),mres($conf),mres($server_ip),mres($campaign_id));
                        $rslt = mysql_query($stmt, $link);
                        $icnt++;
                    }
                }
            } elseif ($rcnt > ($oivr_virtual_agents + $oivr_reserve_agents)) {
                $dcnt = $rcnt - ($oivr_virtual_agents + $oivr_reserve_agents);
                $stmt=sprintf("DELETE FROM osdial_remote_agents WHERE user_start LIKE 'va%s%%' ORDER BY user_start DESC LIMIT %s;",mres($campaign_id),$dcnt);
                $rslt = mysql_query($stmt, $link);
            }

            # Insert any needed user records.
            $urecs = get_krh($link, 'osdial_users', 'user_id,user','',sprintf("user LIKE 'va%s%%'",mres($campaign_id)),'');
            $ucnt = count($urecs);
            if ($ucnt < ($oivr_virtual_agents + $oivr_reserve_agents)) {
                $icnt = 0;
                $unum = 0;
                while ($icnt < (($oivr_virtual_agents + $oivr_reserve_agents) - $ucnt)) {
                    $unum++;
                    $usr = 'va' . $campaign_id . sprintf('%03d', $unum);
                    $ufnd = 0;
                    foreach ($urecs as $urec) {
                        if ($urec['user'] == $usr) {
                            $ufnd++;
                        }
                    }
                    if ($ufnd == 0) {
                        $stmt = "INSERT INTO osdial_users (user,pass,full_name,user_level,user_group) ";
                        $stmt .= sprintf("VALUES ('%s','ViRtUaLaGeNt','Virtual Agent','7','VIRTUAL');",mres($usr));
                        $rslt = mysql_query($stmt, $link);
                        $icnt++;
                    }
                }
            } elseif ($ucnt > ($oivr_virtual_agents + $oivr_reserve_agents)) {
                $dcnt = $ucnt - ($oivr_virtual_agents + $oivr_reserve_agents);
                $stmt=sprintf("DELETE FROM osdial_users WHERE user LIKE 'va%s%%' ORDER BY user DESC LIMIT %s;",mres($campaign_id),$dcnt);
                $rslt = mysql_query($stmt, $link);
            }

            if ($oivr_status == 'ACTIVE') {
                $stmt=sprintf("UPDATE osdial_remote_agents SET status='ACTIVE' WHERE user_start LIKE 'va%s%%';",mres($campaign_id));
                $rslt = mysql_query($stmt, $link);
            } else {
                $stmt=sprintf("UPDATE osdial_remote_agents SET status='INACTIVE' WHERE user_start LIKE 'va%s%%';",mres($campaign_id));
                $rslt = mysql_query($stmt, $link);
            }

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|MODIFY OIVR|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $id = $form_id;
        $SUB = "2keys";
        $ADD = "3menu"; # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=4keys modify keys
######################
if ($ADD == "4keys") {
    if ($LOG['modify_campaigns'] == 1) {
        if (($oivr_opt_id == 0) or (OSDstrlen($oivr_opt_keypress) < 1)) {
            echo $oivr_opt_id . '/' . $oivr_opt_keypress;
            echo "<br><font color=red>KEY NOT MODIFIED - Please go back and look at the data you entered\n";
        } else {
            echo "<br><B><font color=$default_text>KEY MODIFIED: $oivr_opt_id - $oivr_opt_action</font></B>\n";
            $field_name = OSDstrtoupper($field_name);
            $stmt=sprintf("UPDATE osdial_ivr_options SET keypress='%s',action='%s',action_data='%s',ivr_id='%s',parent_id='%s' WHERE id='%s';",mres($oivr_opt_keypress),mres($oivr_opt_action),mres($oivr_opt_action_data),mres($oivr_id),mres($oivr_opt_parent_id),mres($oivr_opt_id));
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|MODIFY FIELD|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $id = $form_id;
        #$SUB = "2fields";
        #$ADD = "3menu";
        $ADD = "3keys";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=6keys delete field
######################
if ($ADD == "6keys") {
    if ($LOG['modify_campaigns'] == 1) {
        if ($oivr_opt_id < 1) {
            echo "<br><font color=red>KEYPRESS NOT DELETED - Could not find field id!\n";
        } else {
            echo "<br><B><font color=$default_text>KEYPRESS DELETED: $oivr_opt_id - $oivr_opt_action</font></B>\n";
            $stmt=sprintf("DELETE FROM osdial_ivr_options WHERE id='%s';",mres($oivr_opt_id));
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|DELETE OIVRO|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $id = $form_id;
        $SUB = "2keys";
        $ADD = "3menu"; # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=35 display campaign ivr menu & keys
######################
if ($ADD == "3menu") {
    echo "<center><br><font class=top_header color=$default_text size=+1>INBOUND/OUTBOUND IVR</font><br><br>\n";

    $oivr = get_first_record($link, 'osdial_ivr', '*', sprintf("campaign_id='%s'",mres($campaign_id)));

    echo '<form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
    echo '<input type="hidden" name="ADD" value="4menu">';
    echo '<input type="hidden" name="oivr_id" value="' . $oivr['id'] . '">';
    echo '<input type="hidden" name="campaign_id" value="' . $campaign_id . '">';

    echo "<table cellspacing=1 cellpadding=5>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Name</td>\n";
    echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="30" maxlength="50" name="oivr_name" value="' . $oivr['name'] . '"></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Announcement File</td>\n";
    echo '      <td bgcolor="' . $oddrows . '">';
    echo ivr_file_text_options($link, 'oivr_announcement', $oivr['announcement'], 20, 50);
    #echo media_file_text_options($link, 'oivr_announcement', $oivr['announcement'], 20, 50);
    #echo '          <select name="oivr_announcement">';
    #echo media_file_select_options($link,$oivr['announcement']);
    #echo "          </select><br>";
    echo "          <br>";
    echo '          <input type="file" name="recfile">';
    echo '      </td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Announcement Repeat Attempt</td>\n";
    echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="4" maxlength="2" name="oivr_repeat_loops" value="' . $oivr['repeat_loops'] . '"></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Wait for Key Attempts</td>\n";
    echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="4" maxlength="2" name="oivr_wait_loops" value="' . $oivr['wait_loops'] . '"></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Wait Period per Attempt (ms)</td>\n";
    echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="4" maxlength="4" name="oivr_wait_timeout" value="' . $oivr['wait_timeout'] . '"></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Answered Status:</td>\n";
    echo '      <td bgcolor="' . $oddrows . '"><select name="oivr_answered_status"><option value="">-NONE-</option>';
    $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
    foreach ($status as $stat) {
        $sel = '';
        if ($stat['status'] == $oivr['answered_status']) {
            $sel = ' selected';
        }
        echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
    }
    echo "  </select></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Virtual Agents</td>\n";
    echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="4" maxlength="3" name="oivr_virtual_agents" value="' . $oivr['virtual_agents'] . '"> <font size=-1></font></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Reserve Agents</td>\n";
    echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="4" maxlength="3" name="oivr_reserve_agents" value="' . $oivr['reserve_agents'] . '"> <font size=-1> Set to 10+ if Inbound.</font></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Allow Inbound</td>\n";
    echo '      <td bgcolor="' . $oddrows . '"><select name="oivr_allow_inbound"><option>Y</option><option>N</option><option selected>' . $oivr['allow_inbound'] . '</option></select></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Status</td>\n";
    echo '      <td bgcolor="' . $oddrows . '">';
    echo '          <select name="oivr_status">';
    if ($oivr['status'] == 'ACTIVE') {
        $asel = ' selected';
    } else {
        $isel = ' selected';
    }
    echo "              <option value=\"ACTIVE\"$asel>ACTIVE</option>";
    echo "              <option value=\"INACTIVE\"$isel>INACTIVE</option>";
    echo '          </select>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Timeout Action</td>\n";
    echo '      <td bgcolor="' . $oddrows . '">';
    echo '         <select name="oivr_timeout_action">';
    echo "              <option value=\"\"> - NONE - </option>";
    $keys = get_krh($link, 'osdial_ivr_options', 'keypress','',sprintf("ivr_id='%s' AND parent_id='%s'",mres($oivr_id),mres($oivr_opt_parent_id)),'');
    $tkey = '';
    if (is_array($keys)) {
        foreach ($keys as $key) {
            $tkey .= $key['keypress'];
        }
    }
    if ( OSDpreg_match('/0/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '0') $sel=' selected'; echo ' <option value="0"' . $sel . '> - 0 -</option>'; }
    if ( OSDpreg_match('/1/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '1') $sel=' selected'; echo ' <option value="1"' . $sel . '> - 1 -</option>'; }
    if ( OSDpreg_match('/2/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '2') $sel=' selected'; echo ' <option value="2"' . $sel . '> - 2 -</option>'; }
    if ( OSDpreg_match('/3/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '3') $sel=' selected'; echo ' <option value="3"' . $sel . '> - 3 -</option>'; }
    if ( OSDpreg_match('/4/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '4') $sel=' selected'; echo ' <option value="4"' . $sel . '> - 4 -</option>'; }
    if ( OSDpreg_match('/5/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '5') $sel=' selected'; echo ' <option value="5"' . $sel . '> - 5 -</option>'; }
    if ( OSDpreg_match('/6/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '6') $sel=' selected'; echo ' <option value="6"' . $sel . '> - 6 -</option>'; }
    if ( OSDpreg_match('/7/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '7') $sel=' selected'; echo ' <option value="7"' . $sel . '> - 7 -</option>'; }
    if ( OSDpreg_match('/8/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '8') $sel=' selected'; echo ' <option value="8"' . $sel . '> - 8 -</option>'; }
    if ( OSDpreg_match('/9/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '9') $sel=' selected'; echo ' <option value="9"' . $sel . '> - 9 -</option>'; }
    if ( OSDpreg_match('/\#/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '#') $sel=' selected'; echo ' <option value="#"' . $sel . '> - # -</option>'; }
    if ( OSDpreg_match('/\*/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '*') $sel=' selected'; echo ' <option value="*"' . $sel . '> - * -</option>'; }
    if ( OSDpreg_match('/i/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == 'i') $sel=' selected'; echo ' <option value="i"' . $sel . '> - Invalid -</option>'; }
    echo '         </select>';
    echo '      </td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Allow Agent Extensions</td>\n";
    echo '      <td bgcolor="' . $oddrows . '"><select name="oivr_allow_agent_extensions"><option>Y</option><option>N</option><option selected>' . $oivr['allow_agent_extensions'] . '</option></select></td>';
    echo "  </tr>\n";
    echo "  <tr class=tabfooter>\n";
    echo "      <td colspan=2 class=tabbutton align=center><input type=submit value=\"Save Form\"></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";

    echo "</form>";

    echo "<br /><br /><hr width=50%>\n";
    echo "<center><font color=$default_text size=+1>KEYPRESS AND ACTIONS</font><br><br>\n";
    echo "<table bgcolor=grey class=shadedtable width=$section_width cellspacing=1 cellpadding=0>\n";
    echo "  <tr class=tabheader>\n";
    echo "      <td align=center>KEYPRESS</td>\n";
    echo "      <td align=center>ACTION</td>\n";
    echo "      <td align=center>DISPOSITION</td>\n";
    echo "      <td align=center colspan=2>ACTIONS</td>\n";
    echo "  </tr>\n";
    $oivr_opts = get_krh($link, 'osdial_ivr_options', '*', 'keypress', sprintf("ivr_id='%s' AND parent_id='0'",mres($oivr['id'])),'');
    $cnt = 0;
    if (is_array($oivr_opts)) {
        foreach ($oivr_opts as $opt) {
            $ad  = explode('#:#',$opt['action_data']);
            echo '  <form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
            echo '  <input type="hidden" name="ADD" value="3keys">';
            echo '  <input type="hidden" name="oivr_id" value="' . $oivr['id'] . '">';
            echo '  <input type="hidden" name="campaign_id" value="' . $campaign_id . '">';
            echo '  <input type="hidden" name="oivr_opt_id" value="' . $opt['id'] . '">';
            echo "  <tr " . bgcolor($cnt) . " class=\"row font1\">";
            $kplabel = $opt['keypress'];
            if ($kplabel=='A') $kplabel='-Extensions-';
            if ($kplabel=='i') $kplabel='-Invalid-';
            echo "      <td align=center>" . $kplabel . "</td>";
            echo "      <td align=center>" . $opt['action'] . "</td>";
            if ($opt['action'] == 'MENU') {
                echo "      <td align=center>" . $ad[6] . "</td>";
            } else {
                echo "      <td align=center>" . $ad[1] . "</td>";
            }
            if (OSDpreg_match('/A/',$opt['keypress'])) {
                echo "      <td align=center colspan=2 class=tabbutton1><input type=submit value=\"Edit\"></td>\n";
            } else {
                echo "      <td align=center><a href=$PHP_SELF?ADD=6keys&campaign_id=" . $campaign_id . "&oivr_id=" . $oivr['id'] . "&oivr_opt_id=" . $opt['id'] . ">DELETE</a></td>\n";
                echo "      <td align=center class=tabbutton1><input type=submit value=\"Edit\"></td>\n";
            }
            echo "  </tr>";
            echo "  </form>";
            $cnt++;
        }
    }
    echo '  <form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
    echo '  <input type="hidden" name="ADD" value="2keys">';
    echo '  <input type="hidden" name="oivr_id" value="' . $oivr['id'] . '">';
    echo '  <input type="hidden" name="campaign_id" value="' . $campaign_id . '">';
    echo "  <tr class=tabfooter>\n";
    echo "      <td align=center class=tabinput>\n";
    echo "        <select name=\"oivr_opt_keypress\">\n";
    echo "          <option value=\"\" selected> - SELECT DIGIT -</option>\n";
    $keys = get_krh($link, 'osdial_ivr_options', 'keypress','',sprintf("ivr_id='%s' AND parent_id='%s'",mres($oivr_id),mres($oivr_opt_parent_id)),'');
    $tkey = '';
    if (is_array($keys)) {
        foreach ($keys as $key) {
            $tkey .= $key['keypress'];
        }
    }
    if ( ! OSDpreg_match('/0/', $tkey) ) { echo ' <option value="0"> - 0 -</option>'; }
    if ( ! OSDpreg_match('/1/', $tkey) ) { echo ' <option value="1"> - 1 -</option>'; }
    if ( ! OSDpreg_match('/2/', $tkey) ) { echo ' <option value="2"> - 2 -</option>'; }
    if ( ! OSDpreg_match('/3/', $tkey) ) { echo ' <option value="3"> - 3 -</option>'; }
    if ( ! OSDpreg_match('/4/', $tkey) ) { echo ' <option value="4"> - 4 -</option>'; }
    if ( ! OSDpreg_match('/5/', $tkey) ) { echo ' <option value="5"> - 5 -</option>'; }
    if ( ! OSDpreg_match('/6/', $tkey) ) { echo ' <option value="6"> - 6 -</option>'; }
    if ( ! OSDpreg_match('/7/', $tkey) ) { echo ' <option value="7"> - 7 -</option>'; }
    if ( ! OSDpreg_match('/8/', $tkey) ) { echo ' <option value="8"> - 8 -</option>'; }
    if ( ! OSDpreg_match('/9/', $tkey) ) { echo ' <option value="9"> - 9 -</option>'; }
    if ( ! OSDpreg_match('/\#/', $tkey) ) { echo ' <option value="#"> - # -</option>'; }
    if ( ! OSDpreg_match('/\*/', $tkey) ) { echo ' <option value="*"> - * -</option>'; }
    if ( ! OSDpreg_match('/i/', $tkey) ) { echo ' <option value="i"> - Invalid -</option>'; }
    echo "      </select>\n";
    echo "    </td>\n";
    echo "    <td align=center class=tabinput>\n";
    echo "      <select name=\"oivr_opt_action\">\n";
    echo "        <option value=\"\"> - Select an Action - </option>\n";
    echo "        <option value=\"PLAYFILE\">Play an Audio File</option>\n";
    echo "        <option value=\"PLAYFILE_FIELD\">Play Audio File from Given Field</option>\n";
    echo "        <option value=\"XFER_EXTERNAL\">Transfer to an Extension/Number</option>\n";
    echo "        <option value=\"XFER_EXTERNAL_MULTI\">Transfer to One of Multiple Extensions/Numbers</option>\n";
    echo "        <option value=\"XFER_INGROUP\">Transfer to an In-Group</option>\n";
    echo "        <option value=\"TVC_LOOKUP\">TVC Lookup</option>\n";
    echo "        <option value=\"HANGUP\">Disposition and Hangup</option>\n";
    echo "        <option value=\"MENU\">Sub-menu</option>\n";
    echo "        <option value=\"MENU_REPEAT\">Repeat the Menu (no-diposition)</option>\n";
    echo "        <option value=\"MENU_EXIT\">Exit from Menu (no-diposition)</option>\n";
    echo "      </select>\n";
    echo "    </td>\n";
    echo "    <td align=center></td>\n";
    echo "    <td align=center colspan=2 class=tabbutton1><input type=submit value=\"New\"></td>\n";
    echo "  </tr>\n";
    echo "  </form>\n";
    echo "</table>\n";

}

######################
# ADD=3keys modify a key
######################
if ($ADD == "3keys") {
    $opt = get_first_record($link, 'osdial_ivr_options', '*', sprintf("id='%s'",mres($oivr_opt_id)));
    echo "<center><br><font color=$default_text size=+1>MODIFY KEYPRESS ACTION</font><br><br>\n";

    echo '<form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
    echo '<input type="hidden" name="ADD" value="4keys">';
    echo '<input type="hidden" name="oivr_id" value="' . $oivr_id . '">';
    echo '<input type="hidden" name="oivr_opt_id" value="' . $opt['id'] . '">';
    echo '<input type="hidden" name="oivr_opt_parent_id" value="' . $opt['parent_id'] . '">';
    echo '<input type="hidden" name="campaign_id" value="' . $campaign_id . '">';
    echo '<input type="hidden" name="oivr_opt_action" value="' . $opt['action'] . '">';

    echo "<table cellspacing=1 cellpadding=5>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Campaign/IVR:</td>\n";
    echo '      <td bgcolor="' . $oddrows . '">' . $campaign_id . '/' . $oivr_id .'</td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td bgcolor=$oddrows align=right>Key:</td>\n";
    echo "    <td bgcolor=$oddrows align=left>";
    $kpdis=''; if ($opt['keypress'] == 'A') $kpdis='disabled';
    echo '      <select ' . $kpdis . ' name="oivr_opt_keypress">';
    if ($opt['keypress'] == 'i') {
        echo '        <option value="' . $opt['keypress'] . '" selected> - Invalid -</option>';
    } else {
        echo '        <option value="' . $opt['keypress'] . '" selected> - ' . $opt['keypress'] . ' -</option>';
    }
    $keys = get_krh($link, 'osdial_ivr_options', 'keypress','',sprintf("ivr_id='%s' AND parent_id='%s'",mres($oivr_id),mres($opt['parent_id'])),'');
    $tkey = '';
    foreach ($keys as $key) {
        $tkey .= $key['keypress'];
    }
    if ( ! OSDpreg_match('/0/', $tkey) ) { echo '        <option value="0"> - 0 -</option>'; }
    if ( ! OSDpreg_match('/1/', $tkey) ) { echo '        <option value="1"> - 1 -</option>'; }
    if ( ! OSDpreg_match('/2/', $tkey) ) { echo '        <option value="2"> - 2 -</option>'; }
    if ( ! OSDpreg_match('/3/', $tkey) ) { echo '        <option value="3"> - 3 -</option>'; }
    if ( ! OSDpreg_match('/4/', $tkey) ) { echo '        <option value="4"> - 4 -</option>'; }
    if ( ! OSDpreg_match('/5/', $tkey) ) { echo '        <option value="5"> - 5 -</option>'; }
    if ( ! OSDpreg_match('/6/', $tkey) ) { echo '        <option value="6"> - 6 -</option>'; }
    if ( ! OSDpreg_match('/7/', $tkey) ) { echo '        <option value="7"> - 7 -</option>'; }
    if ( ! OSDpreg_match('/8/', $tkey) ) { echo '        <option value="8"> - 8 -</option>'; }
    if ( ! OSDpreg_match('/9/', $tkey) ) { echo '        <option value="9"> - 9 -</option>'; }
    if ( ! OSDpreg_match('/\#/', $tkey) ) { echo '        <option value="#"> - # -</option>'; }
    if ( ! OSDpreg_match('/\*/', $tkey) ) { echo '        <option value="*"> - * -</option>'; }
    if ( ! OSDpreg_match('/i/', $tkey) ) { echo '        <option value="i"> - Invalid -</option>'; }
    echo "      </select>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Action:</td>\n";
    echo '      <td bgcolor="' . $oddrows . '">' . $opt['action'] . '</td>';
    echo "  </tr>\n";



    $o = $opt['action'];
    $ad  = explode('#:#',$opt['action_data']);
    if ($o == 'PLAYFILE') {
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi1', $ad[0], 20, 50);
        #echo media_file_text_options($link, 'oi1', $ad[0], 20, 50);
    	#echo '          <select name="oi1">';
        #echo media_file_select_options($link,$ad[0]);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel = '';
            if ($stat['status'] == $ad[1]) {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo '<input type="hidden" name="oi3" value="' . $ad[2] . '">';
    } elseif ($o == 'PLAYFILE_FIELD') { 
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows align=right>Field to Use</td>\n";
        echo '    <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi1">';
        $fldsel=''; if ($ad[0]=='custom1') $fldsel="selected";
        echo '        <option value="custom1" '.$fldsel.'>custom1</option>';
        $fldsel=''; if ($ad[0]=='custom2') $fldsel="selected";
        echo '        <option value="custom2" '.$fldsel.'>custom2</option>';
        echo "      </select>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '    <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel = '';
            if ($stat['status'] == $ad[1]) {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows align=right>Hangup After?</td>\n";
        echo '    <td bgcolor="' . $oddrows . '">';
        $pcchk = ""; if ($ad[2] == 'Y') $pcchk = "checked";
        echo "      <input type=\"checkbox\" name=\"oi3\" value=\"Y\" $pcchk>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    } elseif ($o == 'HANGUP') {
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Hangup (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi1', $ad[0], 20, 50);
        #echo media_file_text_options($link, 'oi1', $ad[0], 20, 50);
    	#echo '          <select name="oi1">';
        #echo media_file_select_options($link,$ad[0]);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel = '';
            if ($stat['status'] == $ad[1]) {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
    } elseif ($o == 'MENU') { 
        echo '<input type="hidden" name="oi1" value="' . $ad[0] . '">';
        echo '<input type="hidden" name="oi2" value="' . $ad[1] . '">';
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Announcement Recording</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi3', $ad[2], 20, 50);
        #echo media_file_text_options($link, 'oi3', $ad[2], 20, 50);
    	#echo '          <select name="oi3">';
        #echo media_file_select_options($link,$ad[2]);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Announcement Repeat Attempts</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="3" maxlength="2" name="oi4" value="' . $ad[3] . '"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Wait for Key Attempts</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="3" maxlength="2" name="oi5" value="' . $ad[4] . '"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Wait for Key Timeout (ms)</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="5" maxlength="4" name="oi6" value="' . $ad[5] . '"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Answered Status:</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><select name="oi7"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel = '';
            if ($stat['status'] == $ad[6]) {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Timeout Action</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '         <select name="oi8">';
    	echo "              <option value=\"\"> - NONE - </option>";
        $keys = get_krh($link, 'osdial_ivr_options', 'keypress','',sprintf("ivr_id='%s' AND parent_id='%s'",mres($oivr['id']),mres($oivr_opt_id)),'');
        $tkey = '';
        foreach ($keys as $key) {
            $tkey .= $key['keypress'];
        }
        if ( OSDpreg_match('/0/', $tkey) ) { $sel=''; if ($ad[7] == '0') $sel=' selected'; echo ' <option value="0"' . $sel . '> - 0 -</option>'; }
        if ( OSDpreg_match('/1/', $tkey) ) { $sel=''; if ($ad[7] == '1') $sel=' selected'; echo ' <option value="1"' . $sel . '> - 1 -</option>'; }
        if ( OSDpreg_match('/2/', $tkey) ) { $sel=''; if ($ad[7] == '2') $sel=' selected'; echo ' <option value="2"' . $sel . '> - 2 -</option>'; }
        if ( OSDpreg_match('/3/', $tkey) ) { $sel=''; if ($ad[7] == '3') $sel=' selected'; echo ' <option value="3"' . $sel . '> - 3 -</option>'; }
        if ( OSDpreg_match('/4/', $tkey) ) { $sel=''; if ($ad[7] == '4') $sel=' selected'; echo ' <option value="4"' . $sel . '> - 4 -</option>'; }
        if ( OSDpreg_match('/5/', $tkey) ) { $sel=''; if ($ad[7] == '5') $sel=' selected'; echo ' <option value="5"' . $sel . '> - 5 -</option>'; }
        if ( OSDpreg_match('/6/', $tkey) ) { $sel=''; if ($ad[7] == '6') $sel=' selected'; echo ' <option value="6"' . $sel . '> - 6 -</option>'; }
        if ( OSDpreg_match('/7/', $tkey) ) { $sel=''; if ($ad[7] == '7') $sel=' selected'; echo ' <option value="7"' . $sel . '> - 7 -</option>'; }
        if ( OSDpreg_match('/8/', $tkey) ) { $sel=''; if ($ad[7] == '8') $sel=' selected'; echo ' <option value="8"' . $sel . '> - 8 -</option>'; }
        if ( OSDpreg_match('/9/', $tkey) ) { $sel=''; if ($ad[7] == '9') $sel=' selected'; echo ' <option value="9"' . $sel . '> - 9 -</option>'; }
        if ( OSDpreg_match('/\#/', $tkey) ) { $sel=''; if ($ad[7] == '#') $sel=' selected'; echo ' <option value="#"' . $sel . '> - # -</option>'; }
        if ( OSDpreg_match('/\*/', $tkey) ) { $sel=''; if ($ad[7] == '*') $sel=' selected'; echo ' <option value="*"' . $sel . '> - * -</option>'; }
        if ( OSDpreg_match('/i/', $tkey) ) { $sel=''; if ($ad[7] == 'i') $sel=' selected'; echo ' <option value="i"' . $sel . '> - Invalid -</option>'; }
        echo '         </select>';
        echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Allow Agent Extensions</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><select name="oi9"><option>Y</option><option>N</option><option selected>' . $oivr['allow_agent_extensions'] . '</option></select></td>';
        echo "  </tr>\n";
    } elseif ($o == 'MENU_REPEAT') { 
        echo '<input type="hidden" name="oi1" value="1">';
    } elseif ($o == 'MENU_EXIT') { 
        echo '<input type="hidden" name="oi1" value="1">';
    } elseif ($o == 'AGENT_EXTENSIONS') { 
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '    <td bgcolor="' . $oddrows . '">';
        echo '      <input type="hidden" name="oi1" value="' . $ad[0] . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel='';
            if ($stat['status'] == $ad[1]) {
                $sel = ' selected';
            }
            echo "        <option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "      </select>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows colspan=2 align=center><i>Note: Agents must have the Agent2Agent option enabled<br>in their user profile in order to receive calls from the IVR.</i></td>\n";
        echo "  </tr>\n";

        $asel = array();
        foreach (explode('|',$ad[2]) as $apck) {
            $a1 = explode(':',$apck);
            if ($a1[0]!='') {
                $asel[$a1[0]]=$a1[1];
            }
        }
        echo "  <tr>\n";
        echo "    <td bgcolor=$oddrows colspan=2 align=center>\n";
        echo "      <table bgcolor=grey cellpadding=0 cellspacing=1>\n";
        echo "        <tr class=tabheader>\n";
        echo "          <td>&nbsp</td>\n";
        echo "          <td>AGENT</td>\n";
        echo "          <td>EXTENSION</td>\n";
        echo "        </tr>\n";
        $camppre=''; if ($LOG['multicomp']) $camppre = OSDsubstr($campaign_id,0,3);
        $agents = get_krh($link, 'osdial_users', 'user,full_name','',sprintf("user_level>3 AND xfer_agent2agent='1' AND user LIKE '%s%%'",mres($camppre)),'');
        foreach ($agents as $agent) {
            echo "        <tr>\n";
            $achk=''; if (isset($asel[$agent['user']])) $achk='checked';
            echo '          <td bgcolor=' . $oddrows . '><input type=checkbox name=chks[] ' . $achk . ' value="' . $agent['user'] . '"></td>' . "\n";
            echo '          <td bgcolor=' . $oddrows . '><span class=font2>' . $agent['user'] . ': ' . $agent['full_name'] . '</span></td>' . "\n";
            if (!isset($asel[$agent['user']])) $asel[$agent['user']] = $agent['user'];
            echo '          <td bgcolor=' . $oddrows . '><input type=text name="ext' . $agent['user'] . '" size=10 value="' . $asel[$agent['user']] . '"></td>' . "\n";
            echo "        </tr>\n";
        }
        echo "        <tr class=tabheader>\n";
        echo "          <td colspan=3></td>\n";
        echo "        </tr>\n";
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    } elseif ($o == 'TVC_LOOKUP') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Description:</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi1" value="' . $ad[0] . '"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel='';
            if ($stat['status'] == $ad[1]) {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Phone# Playback File:</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi3">';
        echo media_file_select_options($link,$ad[2]);
    	echo "          </select>";
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Agent# Playback File:</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi4">';
        echo media_file_select_options($link,$ad[3]);
    	echo "          </select>";
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>In-Group to transfer to</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi5"><option value="">-NONE-</option>';
        $ingroups = get_krh($link, 'osdial_inbound_groups', 'group_id,group_name','',"active='Y'",'');
        foreach ($ingroups as $ing) {
            $sel='';
            if ($ing['group_id'] == $ad[4]) {
                $sel = ' selected';
            }
            echo "<option value=\"" . $ing['group_id'] . "\"". $sel . ">" . $ing['group_id'] . " : " . $ing['group_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>MySQL Server:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi6" value="' . $ad[5] . '"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>MySQL Database:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi7" value="' . $ad[6] . '"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>MySQL User:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi8" value="' . $ad[7] . '"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>MySQL Password:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi9" value="' . $ad[8] . '"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Table:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="255" name="oi10" value="' . $ad[9] . '"></td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Field Mappings:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="50" maxlength="1000" name="oi11" value="' . $ad[10] . '"><br><font size=-1>Format (use pipe to concat): phone_number=dbfld1,first_name=fname,comments=dbfld2|dbfld2|dbfld3</font></td>';
        echo "  </tr>\n";
    } elseif ($o == 'XFER_INGROUP') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi1', $ad[0], 20, 50);
        #echo media_file_text_options($link, 'oi1', $ad[0], 20, 50);
    	#echo '          <select name="oi1">';
        #echo media_file_select_options($link,$ad[0]);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel='';
            if ($stat['status'] == $ad[1]) {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>In-Group to transfer to</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi3"><option value="">-NONE-</option>';
        $ingroups = get_krh($link, 'osdial_inbound_groups', 'group_id,group_name','',"active='Y'",'');
        foreach ($ingroups as $ing) {
            $sel='';
            if ($ing['group_id'] == $ad[2]) {
                $sel = ' selected';
            }
            echo "<option value=\"" . $ing['group_id'] . "\"". $sel . ">" . $ing['group_id'] . " : " . $ing['group_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Failover Method</td>";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '          <select name="oi4">';
        $sel1='';
        $sel2='';
        $sel3='';
        if ($ad[3] == "EXT_NA") {
            $sel2 = ' selected';
        } elseif ($ad[3] == "EXT_UA") {
            $sel3 = ' selected';
        } else {
            $sel1 = ' selected';
        }
        echo '              <option value=""' . $sel1 . '>-NONE-</option>';
        echo '              <option value="EXT_NA"' . $sel2 . '>XFer to ext if no agents logged in.</option>';
        echo '              <option value="EXT_UA"' . $sel3 . '>XFer to ext if agents are unavailable.</option>';
        echo '          </select>';
        echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Extension/Number to Transfer Call to:</td>";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo phone_extension_text_options($link, 'oi5', $ad[4], 15, 30);
        #echo '<input type="text" size="12" maxlength="10" name="oi5" value="' . $ad[4] . '">';
        echo '</td>';
        echo "  </tr>\n";
    } elseif ($o == 'XFER_EXTERNAL') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi1', $ad[0], 20, 50);
        #echo media_file_text_options($link, 'oi1', $ad[0], 20, 50);
    	#echo '          <select name="oi1">';
        #echo media_file_select_options($link,$ad[0]);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel = '';
            if ($stat['status'] == $ad[1]) {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Extension/Number to Transfer Call to:<br>";
        echo "      Format: 9995551212</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        #echo '<input type="text" size="12" maxlength="10" name="oi3" value="' . $ad[2] . '">';
        echo phone_extension_text_options($link, 'oi3', $ad[2], 20, 30);
        echo '</td>';
        echo "  </tr>\n";
    } elseif ($o == 'XFER_EXTERNAL_MULTI') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
        echo ivr_file_text_options($link, 'oi1', $ad[0], 20, 50);
        #echo media_file_text_options($link, 'oi1', $ad[0], 20, 50);
    	#echo '          <select name="oi1">';
        #echo media_file_select_options($link,$ad[0]);
    	#echo "          </select><br>";
    	echo "          <br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'",'');
        foreach ($status as $stat) {
            $sel = '';
            if ($stat['status'] == $ad[1]) {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Phone Number Order</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
	if ($ad[2] == 'ROUNDROBIN') {
		$rrsel = ' selected';
	} else {
		$rnsel = ' selected';
	}
        echo '      <select name="oi3"><option value="ROUNDROBIN"' . $rrsel . '>Round-Robin</option>';
        echo '      <option value="RANDOM"' . $rnsel . '>Random</option></select>';
        echo "  </tr>\n";
        echo "  <tr>\n";
	$tad = $ad;
	array_shift($tad);
	array_shift($tad);
	array_shift($tad);
	$txt = implode("\n",$tad);
        echo "      <td bgcolor=$oddrows align=right>Extensions/Numbers to Transfer Call to:\n";
        echo "      Format: 9995551212</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><textarea name="oi4" cols=10 rows=20>' . $txt . '</textarea></td>';
        echo "  </tr>\n";
    }



    echo "  <tr><td colspan=2 bgcolor=$oddrows>&nbsp;</td></tr>\n";
    echo "  <tr class=tabfooter>\n";
    echo "      <td colspan=2 class=tabbutton align=center><input type=submit value=\"Update Key Entry\"></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";

    echo "</form>";

    if ($o == 'MENU') {
        echo "<br /><br /><hr width=50%>\n";
        echo "<center><font color=$default_text size=+1>KEYPRESS AND ACTIONS</font><br><br>\n";
        echo "<table width=$section_width cellspacing=1 cellpadding=0 bgcolor=grey>\n";
        echo "  <tr class=tabheader>\n";
        echo "      <td align=center>KEYPRESS</td>\n";
        echo "      <td align=center>ACTION</td>\n";
        echo "      <td align=center>DISPOSITION</td>\n";
        echo "      <td align=center colspan=2>ACTIONS</td>\n";
        echo "  </tr>\n";
        $oivr_opts = get_krh($link, 'osdial_ivr_options', '*', 'keypress', sprintf("ivr_id='%s' AND parent_id='%s'",mres($oivr['id']),mres($oivr_opt_id)),'');
        $cnt = 0;
        foreach ($oivr_opts as $opt) {
            $ad  = explode('#:#',$opt['action_data']);
            echo '  <form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
            echo '  <input type="hidden" name="ADD" value="3keys">';
            echo '  <input type="hidden" name="oivr_id" value="' . $oivr['id'] . '">';
            echo '  <input type="hidden" name="campaign_id" value="' . $campaign_id . '">';
            echo '  <input type="hidden" name="oivr_opt_id" value="' . $opt['id'] . '">';
            echo "  <tr " . bgcolor($cnt) . " class=\"row font1\">";
            $kplabel = $opt['keypress'];
            if ($kplabel=='A') $kplabel='-Extensions-';
            if ($kplabel=='i') $kplabel='-Invalid-';
            echo "      <td align=center>" . $kplabel . "</td>";
            echo "      <td align=center>" . $opt['action'] . "</td>";
            echo "      <td align=center>" . $ad[1] . "</td>";
            if (OSDpreg_match('/A/',$opt['keypress'])) {
                echo "      <td align=center class=tabbutton1 colspan=2><input type=submit value=\"Edit\"></td>\n";
            } else {
                echo "      <td align=center><a href=$PHP_SELF?ADD=6keys&campaign_id=" . $campaign_id . "&oivr_id=" . $oivr['id'] . "&oivr_opt_id=" . $opt['id'] . ">DELETE</a></td>\n";
                echo "      <td align=center class=tabbutton1><input type=submit value=\"Edit\"></td>\n";
            }
            echo "  </tr>";
            echo "  </form>";
            $cnt++;
        }
        echo '  <form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
        echo '  <input type="hidden" name="ADD" value="2keys">';
        echo '  <input type="hidden" name="oivr_id" value="' . $oivr['id'] . '">';
        echo '  <input type="hidden" name="oivr_opt_parent_id" value="' . $oivr_opt_id . '">';
        echo '  <input type="hidden" name="campaign_id" value="' . $campaign_id . '">';
        echo "  <tr class=tabfooter>\n";
        echo "      <td align=center class=tabinput>\n";
        echo "        <select name=\"oivr_opt_keypress\">\n";
        echo "          <option value=\"\" selected> - SELECT DIGIT -</option>\n";
        $keys = get_krh($link, 'osdial_ivr_options', 'keypress','',sprintf("ivr_id='%s' AND parent_id='%s'",mres($oivr['id']),mres($oivr_opt_id)),'');
        $tkey = '';
        foreach ($keys as $key) {
            $tkey .= $key['keypress'];
        }
        if ( ! OSDpreg_match('/0/', $tkey) ) { echo ' <option value="0"> - 0 -</option>'; }
        if ( ! OSDpreg_match('/1/', $tkey) ) { echo ' <option value="1"> - 1 -</option>'; }
        if ( ! OSDpreg_match('/2/', $tkey) ) { echo ' <option value="2"> - 2 -</option>'; }
        if ( ! OSDpreg_match('/3/', $tkey) ) { echo ' <option value="3"> - 3 -</option>'; }
        if ( ! OSDpreg_match('/4/', $tkey) ) { echo ' <option value="4"> - 4 -</option>'; }
        if ( ! OSDpreg_match('/5/', $tkey) ) { echo ' <option value="5"> - 5 -</option>'; }
        if ( ! OSDpreg_match('/6/', $tkey) ) { echo ' <option value="6"> - 6 -</option>'; }
        if ( ! OSDpreg_match('/7/', $tkey) ) { echo ' <option value="7"> - 7 -</option>'; }
        if ( ! OSDpreg_match('/8/', $tkey) ) { echo ' <option value="8"> - 8 -</option>'; }
        if ( ! OSDpreg_match('/9/', $tkey) ) { echo ' <option value="9"> - 9 -</option>'; }
        if ( ! OSDpreg_match('/\#/', $tkey) ) { echo ' <option value="#"> - # -</option>'; }
        if ( ! OSDpreg_match('/\*/', $tkey) ) { echo ' <option value="*"> - * -</option>'; }
        if ( ! OSDpreg_match('/i/', $tkey) ) { echo ' <option value="i"> - Invalid -</option>'; }
        echo "      </select>\n";
        echo "    </td>\n";
        echo "    <td align=center class=tabinput>\n";
        echo "      <select name=\"oivr_opt_action\">\n";
        echo "        <option value=\"\"> - Select an Action - </option>\n";
        echo "        <option value=\"PLAYFILE\">Play an Audio File</option>\n";
        echo "        <option value=\"PLAYFILE_FIELD\">Play Audio File from Given Field</option>\n";
        echo "        <option value=\"XFER_EXTERNAL\">Transfer to an Extension/Number</option>\n";
        echo "        <option value=\"XFER_EXTERNAL_MULTI\">Transfer to One of Multiple Extensions/Numbers</option>\n";
        echo "        <option value=\"XFER_INGROUP\">Transfer to an In-Group</option>\n";
        echo "        <option value=\"TVC_LOOKUP\">TVC Lookup</option>\n";
        echo "        <option value=\"HANGUP\">Disposition and Hangup</option>\n";
        echo "        <option value=\"MENU\">Sub-menu</option>\n";
        echo "        <option value=\"MENU_REPEAT\">Repeat the Menu (no-diposition)</option>\n";
        echo "        <option value=\"MENU_EXIT\">Exit from Menu (no-diposition)</option>\n";
        echo "      </select>\n";
        echo "    </td>\n";
        echo "    <td align=center></td>\n";
        echo "    <td align=center colspan=2 class=tabbutton1><input type=submit value=\"New\"></td>\n";
        echo "  </tr>\n";
        echo "  </form>\n";
        echo "</table>\n";
    }
}
?>
