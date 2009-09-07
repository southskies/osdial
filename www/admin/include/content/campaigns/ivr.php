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
    $oivr = get_first_record($link, 'osdial_ivr', '*', "campaign_id='" . $campaign_id . "'");
    if ($oivr['id'] != '') {
        $oivr_id = $oivr['id'];
    } else {
        $SUB = "";
        $ADD = "1menu"; # go to campaign modification form below
    }
}



######################
# ADD=1menu create new menu
######################
if ($ADD == "1menu") {
    if ($LOGmodify_campaigns == 1) {
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
        $oivr_id = 0;
        $oivr = get_first_record($link, 'osdial_ivr', '*', "campaign_id='" . $campaign_id . "'");
        if ($oivr['campaign_id'] != "") {
            $SUB = "2keys";
            $ADD = "3menu"; # go to campaign modification form below
        } else {
            echo "<br><B><font color=$default_text>IVR CREATED</font></B>\n";
            $stmt = "INSERT INTO osdial_ivr (campaign_id) VALUES ('$campaign_id');";
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|CREATE OIVR |$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
            $oivr = get_first_record($link, 'osdial_ivr', '*', "campaign_id='" . $campaign_id . "'");
            $id = $oivr['id'];
            $oivr_id = $oivr['id'];
            $SUB = "2keys";
            $ADD = "3menu"; # go to campaign modification form below
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
        exit;
    }
}

# Format key updates, ie action_data.
if ($ADD == "1keys" or $ADD == '4keys') {
	if ($oivr_opt_action == 'XFER_INGROUP' or $oivr_opt_action == 'HANGUP' or $oivr_opt_action == 'PLAYFILE' or $oivr_opt_action == 'XFER_EXTERNAL' or $oivr_opt_action == 'XFER_EXTERNAL_MULTI' or $oivr_opt_action == 'MENU') {
            # Upload recording
            $recfile = $_FILES['recfile'];
            $recfiletmp = $_FILES['recfile']['tmp_name'];
            $recfilename = $_FILES['recfile']['name'];
            $recfilename = ereg_replace("[^-\_\.0-9a-zA-Z]","",$recfilename);
            if ($recfilename != '') {
                copy($recfiletmp, "/opt/osdial/html/ivr/" . $recfilename);
                if ($oivr_opt_action == 'MENU') {
                    $oi3 = $recfilename;
                } else {
                    $oi1 = $recfilename;
                }
            }
	}
	if ($oivr_opt_action == 'MENU_REPEAT' or $oivr_opt_action == 'MENU_EXIT') {
		$d_ary = array($oi1);
	} elseif ($oivr_opt_action == 'HANGUP') {
		$d_ary = array($oi1,$oi2);
	} elseif ($oivr_opt_action == 'PLAYFILE' or $oivr_opt_action == 'XFER_EXTERNAL') {
		$d_ary = array($oi1,$oi2,$oi3);
	} elseif ($oivr_opt_action == 'XFER_EXTERNAL_MULTI') {
		$oi4 = ereg_replace("\r","",$oi4);
		$oi4 = ereg_replace("\n","#:#",$oi4);
		$d_ary = array($oi1,$oi2,$oi3,$oi4);
	} elseif ($oivr_opt_action == 'XFER_INGROUP') {
		$d_ary = array($oi1,$oi2,$oi3,$oi4,$oi5);
	} elseif ($oivr_opt_action == 'MENU') {
		$d_ary = array($oi1,$oi2,$oi3,$oi4,$oi5,$oi6,$oi7,$oi8);
	} else {
		$d_ary = array($oi1,$oi2,$oi3,$oi4,$oi5,$oi6,$oi7,$oi8,$oi9);
	}
	$oivr_opt_action_data = implode('#:#',$d_ary);
}

######################
# ADD=1keys create new key
######################
if ($ADD == "1keys") {
    if ($LOGmodify_campaigns == 1) {
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
        if (($oivr_id == 0) or (strlen($oivr_opt_keypress) < 1) or (strlen($oivr_opt_action) < 1) or (strlen($oivr_opt_action_data) < 1)) {
            echo "<br><font color=red>KEY NOT CREATED - Please go back and look at the data you entered\n";
            $ADD = "2keys";
        } else {
            echo "<br><B><font color=$default_text>KEY CREATED: $oivr_id - $oivr_opt_action - $oivr_opt_keypress</font></B>\n";
            $stmt = "INSERT INTO osdial_ivr_options (ivr_id,parent_id,keypress,action,action_data) VALUES ('$oivr_id','$oivr_opt_parent_id','$oivr_opt_keypress','$oivr_opt_action','$oivr_opt_action_data');";
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
        exit;
    }
}


######################
# ADD=2keys add a new key
######################
if ($ADD == "2keys" and ($oivr_opt_keypress == '' or $oivr_opt_action == '')) {
    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
    echo "<center><br><font color=$default_text size=+1>YOU MUST SELECT A KEYPRESS AND AN ACTION</font><br><br>\n";
    $ADD = '3menu';
    $SUB = '2keys';
}
if ($ADD == "2keys") {
    echo "<TABLE align=center><TR><TD>\n";
    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
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
    	echo '          <select name="oi1">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
        foreach ($status as $stat) {
            if ($stat['status'] == 'VPLAY') {
                $sel = ' selected';
            }
            echo "<option value=\"" . $stat['status'] . "\"" . $sel . ">" . $stat['status'] . " : " . $stat['status_name'] . "</option>";
        }
        echo "  </select></td>\n";
        echo "  </tr>\n";
        echo '<input type="hidden" name="oi3" value="1">';
    } elseif ($o == 'HANGUP') {
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Hangup (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi1">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
    	echo '          <select name="oi3">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
        	if ($file == $ad[2]) {
            	$sel = ' selected';
        	}
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
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
    } elseif ($o == 'XFER_INGROUP') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi1">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
        $ingroups = get_krh($link, 'osdial_inbound_groups', 'group_id,group_name','',"active='Y'");
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
        echo "      <td bgcolor=$oddrows align=right>Ext/Number to Transfer Call to:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="12" maxlength="10" name="oi5" value=""></td>';
        echo "  </tr>\n";
    } elseif ($o == 'XFER_EXTERNAL') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi1">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
        echo "      <td bgcolor=$oddrows align=right>Phone Number to Transfer Call to:<br>";
        echo "      Format: 9995551212</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="12" maxlength="10" name="oi3" value=""></td>';
        echo "  </tr>\n";
    } elseif ($o == 'XFER_EXTERNAL_MULTI') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi1">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
        echo "      <td bgcolor=$oddrows align=right>Phone Number to Transfer Call to:\n";
        echo "      Format: 9995551212</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><textarea name="oi4" cols=10 rows=20></textarea></td>';
        echo "  </tr>\n";
    }



    echo "  <tr><td colspan=2 bgcolor=$oddrows>&nbsp;</td></tr>\n";
    echo "  <tr>\n";
    echo "      <td colspan=2 bgcolor=$menubarcolor align=center><input type=submit value=\"Create Key Entry\"></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";

    echo "</form>";
}




######################
# ADD=4menu modify menu
######################
if ($ADD == "4menu") {
    if ($LOGmodify_campaigns == 1) {
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
        if (($oivr_id < 1) or ($oivr_repeat_loops < 1) or ($oivr_wait_loops < 1) or ($oivr_wait_timeout < 1)) {
            echo "<br><font color=red>IVR NOT MODIFIED - Please go back and look at the data you entered\n";
        } else {
            # Upload recording
            $recfile = $_FILES['recfile'];
            $recfiletmp = $_FILES['recfile']['tmp_name'];
            $recfilename = $_FILES['recfile']['name'];
            $recfilename = ereg_replace("[^-\_\.0-9a-zA-Z]","",$recfilename);
            if ($recfilename != '') {
                copy($recfiletmp, "/opt/osdial/html/ivr/" . $recfilename);
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

            echo "<br><B><font color=$default_text>IVR MODIFIED: $oivr_id - $campaign_id - $oivr_name</font></B>\n";
            $stmt = "UPDATE osdial_ivr SET name='$oivr_name',announcement='$oivr_announcement',repeat_loops='$oivr_repeat_loops',wait_loops='$oivr_wait_loops',wait_timeout='$oivr_wait_timeout',answered_status='$oivr_answered_status',virtual_agents='$oivr_virtual_agents',status='$oivr_status',timeout_action='$oivr_timeout_action',reserve_agents='$oivr_reserve_agents',allow_inbound='$oivr_allow_inbound' where id='$oivr_id';";
            $rslt = mysql_query($stmt, $link);

            $svr = get_first_record($link, 'servers', 'server_ip',"");
            $server_ip = $svr['server_ip'];
            # Insert Virtual Agents.
            $rma = get_krh($link, 'osdial_remote_agents', 'remote_agent_id,user_start','',"user_start LIKE 'va" . $campaign_id . "%'");
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
                        $stmt = "INSERT INTO osdial_remote_agents (user_start,conf_exten,server_ip,campaign_id) VALUES ";
                        $conf = '87' . sprintf('%03d',$oivr_id) . sprintf('%03d',$unum);
                        $stmt .= "('$usr','$conf','$server_ip','$campaign_id');";
                        $rslt = mysql_query($stmt, $link);
                        $icnt++;
                    }
                }
            } elseif ($rcnt > ($oivr_virtual_agents + $oivr_reserve_agents)) {
                $dcnt = $rcnt - ($oivr_virtual_agents + $oivr_reserve_agents);
                $stmt = "DELETE FROM osdial_remote_agents WHERE user_start LIKE 'va$campaign_id%' ORDER BY user_start DESC LIMIT $dcnt;";
                $rslt = mysql_query($stmt, $link);
            }

            # Insert any needed user records.
            $urecs = get_krh($link, 'osdial_users', 'user_id,user','',"user LIKE 'va$campaign_id%'");
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
                        $stmt .= "VALUES ('$usr','ViRtUaLaGeNt','Virtual Agent','4','VIRTUAL');";
                        $rslt = mysql_query($stmt, $link);
                        $icnt++;
                    }
                }
            } elseif ($ucnt > ($oivr_virtual_agents + $oivr_reserve_agents)) {
                $dcnt = $ucnt - ($oivr_virtual_agents + $oivr_reserve_agents);
                $stmt = "DELETE FROM osdial_users WHERE user LIKE 'va$campaign_id%' ORDER BY user DESC LIMIT $dcnt;";
                $rslt = mysql_query($stmt, $link);
            }

            if ($oivr_status == 'ACTIVE') {
                $stmt = "UPDATE osdial_remote_agents SET status='ACTIVE' WHERE user_start LIKE 'va$campaign_id%';";
                $rslt = mysql_query($stmt, $link);
            } else {
                $stmt = "UPDATE osdial_remote_agents SET status='INACTIVE' WHERE user_start LIKE 'va$campaign_id%';";
                $rslt = mysql_query($stmt, $link);
            }

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|MODIFY OIVR|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
        exit;
    }
    $id = $form_id;
    $SUB = "2keys";
    $ADD = "3menu"; # go to campaign modification form below
}

######################
# ADD=4keys modify keys
######################
if ($ADD == "4keys") {
    if ($LOGmodify_campaigns == 1) {
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
        if (($oivr_opt_id == 0) or (strlen($oivr_opt_keypress) < 1)) {
            echo $oivr_opt_id . '/' . $oivr_opt_keypress;
            echo "<br><font color=red>KEY NOT MODIFIED - Please go back and look at the data you entered\n";
        } else {
            echo "<br><B><font color=$default_text>KEY MODIFIED: $oivr_opt_id - $oivr_opt_action</font></B>\n";
            $field_name = strtoupper($field_name);
            $stmt = "UPDATE osdial_ivr_options SET keypress='$oivr_opt_keypress',action='$oivr_opt_action',action_data='$oivr_opt_action_data',ivr_id='$oivr_id',parent_id='$oivr_opt_parent_id' where id='$oivr_opt_id';";
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|MODIFY FIELD|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
        exit;
    }
    $id = $form_id;
    #$SUB = "2fields";
    #$ADD = "3menu";
    $ADD = "3keys";
}


######################
# ADD=6keys delete field
######################
if ($ADD == "6keys") {
    if ($LOGmodify_campaigns == 1) {
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
        if ($oivr_opt_id < 1) {
            echo "<br><font color=red>KEYPRESS NOT DELETED - Could not find field id!\n";
        } else {
            echo "<br><B><font color=$default_text>KEYPRESS DELETED: $oivr_opt_id - $oivr_opt_action</font></B>\n";
            $stmt = "DELETE FROM osdial_ivr_options WHERE id='$oivr_opt_id';";
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|DELETE OIVRO|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
        exit;
    }
    $id = $form_id;
    $SUB = "2keys";
    $ADD = "3menu"; # go to campaign modification form below
}


######################
# ADD=35 display campaign ivr menu & keys
######################
if ($ADD == "3menu") {
    echo "<TABLE align=center><TR><TD>\n";
    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
    echo "<center><br><font color=$default_text size=+1>INBOUND/OUTBOUND IVR</font><br><br>\n";

    $oivr = get_first_record($link, 'osdial_ivr', '*', "campaign_id='" . $campaign_id . "'");

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
    echo '          <select name="oivr_announcement">';
    echo "              <option value=\"\"> - NONE - </option>";
    $path = "/opt/osdial/html/ivr";
    $dir = @opendir($path);
    while ($file = readdir($dir)) {
        $sel = '';
        if ($file == $oivr['announcement']) {
            $sel = ' selected';
        }
	if ($file != '.' and $file != '..') {
        	echo "              <option $sel>$file</option>";
	}
    }
    echo "          </select><br>";
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
    $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
    $keys = get_krh($link, 'osdial_ivr_options', 'keypress','',"ivr_id='" . $oivr_id . "' AND parent_id='" . $oivr_opt_parent_id . "'");
    $tkey = '';
    foreach ($keys as $key) {
        $tkey .= $key['keypress'];
    }
    if ( preg_match('/0/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '0') $sel=' selected'; echo ' <option value="0"' . $sel . '> - 0 -</option>'; }
    if ( preg_match('/1/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '1') $sel=' selected'; echo ' <option value="1"' . $sel . '> - 1 -</option>'; }
    if ( preg_match('/2/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '2') $sel=' selected'; echo ' <option value="2"' . $sel . '> - 2 -</option>'; }
    if ( preg_match('/3/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '3') $sel=' selected'; echo ' <option value="3"' . $sel . '> - 3 -</option>'; }
    if ( preg_match('/4/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '4') $sel=' selected'; echo ' <option value="4"' . $sel . '> - 4 -</option>'; }
    if ( preg_match('/5/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '5') $sel=' selected'; echo ' <option value="5"' . $sel . '> - 5 -</option>'; }
    if ( preg_match('/6/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '6') $sel=' selected'; echo ' <option value="6"' . $sel . '> - 6 -</option>'; }
    if ( preg_match('/7/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '7') $sel=' selected'; echo ' <option value="7"' . $sel . '> - 7 -</option>'; }
    if ( preg_match('/8/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '8') $sel=' selected'; echo ' <option value="8"' . $sel . '> - 8 -</option>'; }
    if ( preg_match('/9/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '9') $sel=' selected'; echo ' <option value="9"' . $sel . '> - 9 -</option>'; }
    if ( preg_match('/\#/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '#') $sel=' selected'; echo ' <option value="#"' . $sel . '> - # -</option>'; }
    if ( preg_match('/\*/', $tkey) ) { $sel=''; if ($oivr['timeout_action'] == '*') $sel=' selected'; echo ' <option value="*"' . $sel . '> - * -</option>'; }
    echo '         </select>';
    echo '      </td>';
    echo "  <tr>\n";
    echo "  <tr><td colspan=2 bgcolor=$oddrows>&nbsp;</td></tr>\n";
    echo "  <tr>\n";
    echo "      <td colspan=2 bgcolor=$menubarcolor align=center><input type=submit value=\"Save Form\"></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";

    echo "</form>";

    echo "<br /><br /><hr width=50%>\n";
    echo "<center><font color=$default_text size=+1>KEYPRESS AND ACTIONS</font><br><br>\n";
    echo "<table width=$section_width cellspacing=1 cellpadding=1>\n";
    echo "  <tr bgcolor=$menubarcolor>\n";
    echo "      <td align=center><font color=white size=1>KEYPRESS</font></td>\n";
    echo "      <td align=center><font color=white size=1>ACTION</font></td>\n";
    echo "      <td align=center><font color=white size=1>DISPOSITION</font></td>\n";
    echo "      <td align=center><font color=white size=1>&nbsp;</font></td>\n";
    echo "  </tr>\n";
    $oivr_opts = get_krh($link, 'osdial_ivr_options', '*', 'keypress', "ivr_id='" . $oivr['id'] . "' AND parent_id='0'");
    $cnt = 0;
    foreach ($oivr_opts as $opt) {
        $ad  = explode('#:#',$opt['action_data']);
        if (eregi("1$|3$|5$|7$|9$",$cnt)) {
            $bgcolor = 'bgcolor="' . $oddrows . '"';
        } else {
            $bgcolor = 'bgcolor="' . $evenrows . '"';
        }
        echo '  <form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
        echo '  <input type="hidden" name="ADD" value="3keys">';
        echo '  <input type="hidden" name="oivr_id" value="' . $oivr['id'] . '">';
        echo '  <input type="hidden" name="campaign_id" value="' . $campaign_id . '">';
        echo '  <input type="hidden" name="oivr_opt_id" value="' . $opt['id'] . '">';
        echo "  <tr>";
        echo "      <td $bgcolor align=center>" . $opt['keypress'] . "</td>";
        echo "      <td $bgcolor align=center>" . $opt['action'] . "</td>";
        if ($opt['action'] == 'MENU') {
            echo "      <td $bgcolor align=center>" . $ad[6] . "</td>";
        } else {
            echo "      <td $bgcolor align=center>" . $ad[1] . "</td>";
        }
        echo "      <td $bgcolor align=center><input type=submit value=\"Edit\">  <a href=$PHP_SELF?ADD=6keys&campaign_id=" . $campaign_id . "&oivr_id=" . $oivr['id'] . "&oivr_opt_id=" . $opt['id'] . ">DELETE</a></td>\n";
        echo "  </tr>";
        echo "  </form>";
        $cnt++;
    }
    echo "  <tr><td colspan=6 bgcolor=$oddrows align=center></td></tr>\n";
    echo '  <form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
    echo '  <input type="hidden" name="ADD" value="2keys">';
    echo '  <input type="hidden" name="oivr_id" value="' . $oivr['id'] . '">';
    echo '  <input type="hidden" name="campaign_id" value="' . $campaign_id . '">';
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=center>";
    echo '<select name="oivr_opt_keypress">';
    echo ' <option value="" selected> - SELECT DIGIT -</option>';
    $keys = get_krh($link, 'osdial_ivr_options', 'keypress','',"ivr_id='" . $oivr_id . "' AND parent_id='" . $oivr_opt_parent_id . "'");
    $tkey = '';
    foreach ($keys as $key) {
        $tkey .= $key['keypress'];
    }
    if ( ! preg_match('/0/', $tkey) ) { echo ' <option value="0"> - 0 -</option>'; }
    if ( ! preg_match('/1/', $tkey) ) { echo ' <option value="1"> - 1 -</option>'; }
    if ( ! preg_match('/2/', $tkey) ) { echo ' <option value="2"> - 2 -</option>'; }
    if ( ! preg_match('/3/', $tkey) ) { echo ' <option value="3"> - 3 -</option>'; }
    if ( ! preg_match('/4/', $tkey) ) { echo ' <option value="4"> - 4 -</option>'; }
    if ( ! preg_match('/5/', $tkey) ) { echo ' <option value="5"> - 5 -</option>'; }
    if ( ! preg_match('/6/', $tkey) ) { echo ' <option value="6"> - 6 -</option>'; }
    if ( ! preg_match('/7/', $tkey) ) { echo ' <option value="7"> - 7 -</option>'; }
    if ( ! preg_match('/8/', $tkey) ) { echo ' <option value="8"> - 8 -</option>'; }
    if ( ! preg_match('/9/', $tkey) ) { echo ' <option value="9"> - 9 -</option>'; }
    if ( ! preg_match('/\#/', $tkey) ) { echo ' <option value="#"> - # -</option>'; }
    if ( ! preg_match('/\*/', $tkey) ) { echo ' <option value="*"> - * -</option>'; }
    echo "</select>\n";
    echo "</td>\n";
    echo "      <td bgcolor=$oddrows align=center><select name=\"oivr_opt_action\">";
    echo "      <option value=\"\"> - Select an Action - </option>";
    echo "      <option value=\"PLAYFILE\">Play an Audio File</option>";
    echo "      <option value=\"XFER_EXTERNAL\">Transfer to an External Number</option>";
    echo "      <option value=\"XFER_EXTERNAL_MULTI\">Transfer to One of Multiple External Numbers</option>";
    echo "      <option value=\"XFER_INGROUP\">Transfer to an In-Group</option>";
    echo "      <option value=\"HANGUP\">Disposition and Hangup</option>";
    echo "      <option value=\"MENU\">Sub-menu</option>";
    echo "      <option value=\"MENU_REPEAT\">Repeat the Menu (no-diposition)</option>";
    echo "      <option value=\"MENU_EXIT\">Exit from Menu (no-diposition)</option>";
    echo "      </td>\n";
    echo "      <td bgcolor=$oddrows align=center></td>\n";
    echo "      <td bgcolor=$menubarcolor align=center><input type=submit value=\"New\"></td>\n";
    echo "  </tr>\n";
    echo "  </form>\n";
    echo "</table>\n";

}

######################
# ADD=3keys modify a key
######################
if ($ADD == "3keys") {
    $opt = get_first_record($link, 'osdial_ivr_options', '*', "id='" . $oivr_opt_id . "'");
    echo "<TABLE align=center><TR><TD>\n";
    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
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
    echo "      <td bgcolor=$oddrows align=right>Key:</td>\n";
    echo "      <td bgcolor=$oddrows align=left>";
    echo '<select name="oivr_opt_keypress">';
    echo ' <option value="' . $opt['keypress'] . '" selected> - ' . $opt['keypress'] . ' -</option>';
    $keys = get_krh($link, 'osdial_ivr_options', 'keypress','',"ivr_id='" . $oivr_id . "' AND parent_id='" . $opt['parent_id'] . "'");
    $tkey = '';
    foreach ($keys as $key) {
        $tkey .= $key['keypress'];
    }
    if ( ! preg_match('/0/', $tkey) ) { echo ' <option value="0"> - 0 -</option>'; }
    if ( ! preg_match('/1/', $tkey) ) { echo ' <option value="1"> - 1 -</option>'; }
    if ( ! preg_match('/2/', $tkey) ) { echo ' <option value="2"> - 2 -</option>'; }
    if ( ! preg_match('/3/', $tkey) ) { echo ' <option value="3"> - 3 -</option>'; }
    if ( ! preg_match('/4/', $tkey) ) { echo ' <option value="4"> - 4 -</option>'; }
    if ( ! preg_match('/5/', $tkey) ) { echo ' <option value="5"> - 5 -</option>'; }
    if ( ! preg_match('/6/', $tkey) ) { echo ' <option value="6"> - 6 -</option>'; }
    if ( ! preg_match('/7/', $tkey) ) { echo ' <option value="7"> - 7 -</option>'; }
    if ( ! preg_match('/8/', $tkey) ) { echo ' <option value="8"> - 8 -</option>'; }
    if ( ! preg_match('/9/', $tkey) ) { echo ' <option value="9"> - 9 -</option>'; }
    if ( ! preg_match('/\#/', $tkey) ) { echo ' <option value="#"> - # -</option>'; }
    if ( ! preg_match('/\*/', $tkey) ) { echo ' <option value="*"> - * -</option>'; }
    echo "</select>\n";
    echo "</td>\n";
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
    	echo '          <select name="oi1">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
        	if ($file == $ad[0]) {
            	$sel = ' selected';
        	}
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
    } elseif ($o == 'HANGUP') {
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Hangup (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi1">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
        	if ($file == $ad[0]) {
            	$sel = ' selected';
        	}
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
	#echo '<input type="text" size="30" maxlength="255" name="oi3" value="' . $ad[2] . '"><br />';
    	echo '          <select name="oi3">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
        	if ($file == $ad[2]) {
            	$sel = ' selected';
        	}
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
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
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
        $keys = get_krh($link, 'osdial_ivr_options', 'keypress','',"ivr_id='" . $oivr['id'] . "' AND parent_id='" . $oivr_opt_id . "'");
        $tkey = '';
        foreach ($keys as $key) {
            $tkey .= $key['keypress'];
        }
        if ( preg_match('/0/', $tkey) ) { $sel=''; if ($ad[7] == '0') $sel=' selected'; echo ' <option value="0"' . $sel . '> - 0 -</option>'; }
        if ( preg_match('/1/', $tkey) ) { $sel=''; if ($ad[7] == '1') $sel=' selected'; echo ' <option value="1"' . $sel . '> - 1 -</option>'; }
        if ( preg_match('/2/', $tkey) ) { $sel=''; if ($ad[7] == '2') $sel=' selected'; echo ' <option value="2"' . $sel . '> - 2 -</option>'; }
        if ( preg_match('/3/', $tkey) ) { $sel=''; if ($ad[7] == '3') $sel=' selected'; echo ' <option value="3"' . $sel . '> - 3 -</option>'; }
        if ( preg_match('/4/', $tkey) ) { $sel=''; if ($ad[7] == '4') $sel=' selected'; echo ' <option value="4"' . $sel . '> - 4 -</option>'; }
        if ( preg_match('/5/', $tkey) ) { $sel=''; if ($ad[7] == '5') $sel=' selected'; echo ' <option value="5"' . $sel . '> - 5 -</option>'; }
        if ( preg_match('/6/', $tkey) ) { $sel=''; if ($ad[7] == '6') $sel=' selected'; echo ' <option value="6"' . $sel . '> - 6 -</option>'; }
        if ( preg_match('/7/', $tkey) ) { $sel=''; if ($ad[7] == '7') $sel=' selected'; echo ' <option value="7"' . $sel . '> - 7 -</option>'; }
        if ( preg_match('/8/', $tkey) ) { $sel=''; if ($ad[7] == '8') $sel=' selected'; echo ' <option value="8"' . $sel . '> - 8 -</option>'; }
        if ( preg_match('/9/', $tkey) ) { $sel=''; if ($ad[7] == '9') $sel=' selected'; echo ' <option value="9"' . $sel . '> - 9 -</option>'; }
        if ( preg_match('/\#/', $tkey) ) { $sel=''; if ($ad[7] == '#') $sel=' selected'; echo ' <option value="#"' . $sel . '> - # -</option>'; }
        if ( preg_match('/\*/', $tkey) ) { $sel=''; if ($ad[7] == '*') $sel=' selected'; echo ' <option value="*"' . $sel . '> - * -</option>'; }
        echo '         </select>';
        echo '      </td>';
        echo "  <tr>\n";
    } elseif ($o == 'MENU_REPEAT') { 
        echo '<input type="hidden" name="oi1" value="1">';
    } elseif ($o == 'MENU_EXIT') { 
        echo '<input type="hidden" name="oi1" value="1">';
    } elseif ($o == 'XFER_INGROUP') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi1">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
        	if ($file == $ad[0]) {
            	$sel = ' selected';
        	}
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
        $ingroups = get_krh($link, 'osdial_inbound_groups', 'group_id,group_name','',"active='Y'");
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
        echo "      <td bgcolor=$oddrows align=right>Ext/Number to Transfer Call to:</td>";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="12" maxlength="10" name="oi5" value="' . $ad[4] . '"></td>';
        echo "  </tr>\n";
    } elseif ($o == 'XFER_EXTERNAL') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi1">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
        	if ($file == $ad[0]) {
            	$sel = ' selected';
        	}
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
        echo "      <td bgcolor=$oddrows align=right>Phone Number to Transfer Call to:<br>";
        echo "      Format: 9995551212</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><input type="text" size="12" maxlength="10" name="oi3" value="' . $ad[2] . '"></td>';
        echo "  </tr>\n";
    } elseif ($o == 'XFER_EXTERNAL_MULTI') { 
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>File to Play Before Transfer (Optional)</td>\n";
    	echo '      <td bgcolor="' . $oddrows . '">';
    	echo '          <select name="oi1">';
    	echo "              <option value=\"\"> - NONE - </option>";
    	$path = "/opt/osdial/html/ivr";
    	$dir = @opendir($path);
    	while ($file = readdir($dir)) {
        	$sel = '';
        	if ($file == $ad[0]) {
            	$sel = ' selected';
        	}
		if ($file != '.' and $file != '..') {
        		echo "              <option $sel>$file</option>";
		}
    	}
    	echo "          </select><br>";
    	echo '          <input type="file" name="recfile">';
    	echo '      </td>';
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=right>Status to Disposition as</td>\n";
        echo '      <td bgcolor="' . $oddrows . '">';
        echo '      <select name="oi2"><option value="">-NONE-</option>';
        $status = get_krh($link, 'osdial_statuses', 'status,status_name','',"status LIKE 'V%'");
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
        echo "      <td bgcolor=$oddrows align=right>Phone Number to Transfer Call to:\n";
        echo "      Format: 9995551212</td>\n";
        echo '      <td bgcolor="' . $oddrows . '"><textarea name="oi4" cols=10 rows=20>' . $txt . '</textarea></td>';
        echo "  </tr>\n";
    }



    echo "  <tr><td colspan=2 bgcolor=$oddrows>&nbsp;</td></tr>\n";
    echo "  <tr>\n";
    echo "      <td colspan=2 bgcolor=$menubarcolor align=center><input type=submit value=\"Update Key Entry\"></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";

    echo "</form>";

    if ($o == 'MENU') {
        echo "<br /><br /><hr width=50%>\n";
        echo "<center><font color=$default_text size=+1>KEYPRESS AND ACTIONS</font><br><br>\n";
        echo "<table width=$section_width cellspacing=1 cellpadding=1>\n";
        echo "  <tr bgcolor=$menubarcolor>\n";
        echo "      <td align=center><font color=white size=1>KEYPRESS</font></td>\n";
        echo "      <td align=center><font color=white size=1>ACTION</font></td>\n";
        echo "      <td align=center><font color=white size=1>DISPOSITION</font></td>\n";
        echo "      <td align=center><font color=white size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        $oivr_opts = get_krh($link, 'osdial_ivr_options', '*', 'keypress', "ivr_id='" . $oivr['id'] . "' AND parent_id='$oivr_opt_id'");
        $cnt = 0;
        foreach ($oivr_opts as $opt) {
            $ad  = explode('#:#',$opt['action_data']);
            if (eregi("1$|3$|5$|7$|9$",$cnt)) {
                $bgcolor = 'bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor = 'bgcolor="' . $evenrows . '"';
            }
            echo '  <form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
            echo '  <input type="hidden" name="ADD" value="3keys">';
            echo '  <input type="hidden" name="oivr_id" value="' . $oivr['id'] . '">';
            echo '  <input type="hidden" name="campaign_id" value="' . $campaign_id . '">';
            echo '  <input type="hidden" name="oivr_opt_id" value="' . $opt['id'] . '">';
            echo "  <tr>";
            echo "      <td $bgcolor align=center>" . $opt['keypress'] . "</td>";
            echo "      <td $bgcolor align=center>" . $opt['action'] . "</td>";
            echo "      <td $bgcolor align=center>" . $ad[1] . "</td>";
            echo "      <td $bgcolor align=center><input type=submit value=\"Edit\">  <a href=$PHP_SELF?ADD=6keys&campaign_id=" . $campaign_id . "&oivr_id=" . $oivr['id'] . "&oivr_opt_id=" . $opt['id'] . ">DELETE</a></td>\n";
            echo "  </tr>";
            echo "  </form>";
            $cnt++;
        }
        echo "  <tr><td colspan=6 bgcolor=$oddrows align=center></td></tr>\n";
        echo '  <form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
        echo '  <input type="hidden" name="ADD" value="2keys">';
        echo '  <input type="hidden" name="oivr_id" value="' . $oivr['id'] . '">';
        echo '  <input type="hidden" name="oivr_opt_parent_id" value="' . $oivr_opt_id . '">';
        echo '  <input type="hidden" name="campaign_id" value="' . $campaign_id . '">';
        echo "  <tr>\n";
        echo "      <td bgcolor=$oddrows align=center>";
        echo '<select name="oivr_opt_keypress">';
        echo ' <option value="" selected> - SELECT DIGIT -</option>';
        $keys = get_krh($link, 'osdial_ivr_options', 'keypress','',"ivr_id='" . $oivr['id'] . "' AND parent_id='" . $oivr_opt_id . "'");
        $tkey = '';
        foreach ($keys as $key) {
            $tkey .= $key['keypress'];
        }
        if ( ! preg_match('/0/', $tkey) ) { echo ' <option value="0"> - 0 -</option>'; }
        if ( ! preg_match('/1/', $tkey) ) { echo ' <option value="1"> - 1 -</option>'; }
        if ( ! preg_match('/2/', $tkey) ) { echo ' <option value="2"> - 2 -</option>'; }
        if ( ! preg_match('/3/', $tkey) ) { echo ' <option value="3"> - 3 -</option>'; }
        if ( ! preg_match('/4/', $tkey) ) { echo ' <option value="4"> - 4 -</option>'; }
        if ( ! preg_match('/5/', $tkey) ) { echo ' <option value="5"> - 5 -</option>'; }
        if ( ! preg_match('/6/', $tkey) ) { echo ' <option value="6"> - 6 -</option>'; }
        if ( ! preg_match('/7/', $tkey) ) { echo ' <option value="7"> - 7 -</option>'; }
        if ( ! preg_match('/8/', $tkey) ) { echo ' <option value="8"> - 8 -</option>'; }
        if ( ! preg_match('/9/', $tkey) ) { echo ' <option value="9"> - 9 -</option>'; }
        if ( ! preg_match('/\#/', $tkey) ) { echo ' <option value="#"> - # -</option>'; }
        if ( ! preg_match('/\*/', $tkey) ) { echo ' <option value="*"> - * -</option>'; }
        echo "</select>\n";
        echo "</td>\n";
        echo "      <td bgcolor=$oddrows align=center><select name=\"oivr_opt_action\">";
        echo "      <option value=\"\"> - Select an Action - </option>";
        echo "      <option value=\"PLAYFILE\">Play an Audio File</option>";
        echo "      <option value=\"XFER_EXTERNAL\">Transfer to an External Number</option>";
        echo "      <option value=\"XFER_EXTERNAL_MULTI\">Transfer to One of Multiple External Numbers</option>";
        echo "      <option value=\"XFER_INGROUP\">Transfer to an In-Group</option>";
        echo "      <option value=\"HANGUP\">Disposition and Hangup</option>";
        echo "      <option value=\"MENU\">Sub-menu</option>";
        echo "      <option value=\"MENU_REPEAT\">Repeat the Menu (no-diposition)</option>";
        echo "      <option value=\"MENU_EXIT\">Exit from Menu (no-diposition)</option>";
        echo "      </td>\n";
        echo "      <td bgcolor=$oddrows align=center></td>\n";
        echo "      <td bgcolor=$menubarcolor align=center><input type=submit value=\"New\"></td>\n";
        echo "  </tr>\n";
        echo "  </form>\n";
        echo "</table>\n";
    }
}
?>
