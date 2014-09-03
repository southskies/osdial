<?php
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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






######################
# ADD=41lx modify lead transfer rules submission
######################
if ($ADD=="41lx") {
    if ($LOG['modify_leads']==1 and $LOG['modify_lists']==1) {
        $new_lead_transfer_id = $lead_transfer_id;
        if ($LOG['multicomp']>0) {
            $new_lead_transfer_id = $LOG['company_prefix'] . $lead_transfer_id;
        }
        $old_lead_transfer_id=strtoupper($old_lead_transfer_id);
        $new_lead_transfer_id=strtoupper($new_lead_transfer_id);
        $lead_transfer_id=strtoupper($lead_transfer_id);

        $rule_error=0;
        # Add rule
        if ($SUB==2) {
            $ret=get_first_record($link, 'osdial_lead_transfers', '*', sprintf("id='%s'",mres($old_lead_transfer_id)));
            $lead_transfer_container = '';
            if (get_variable('lead_transfer_action')=='MOVE') {
                if (!empty($ret['container'])) $lead_transfer_container .= $ret['container'] . '|:';
            }

            $lead_transfer_container .= get_variable('lead_transfer_action') . '|';
            $lead_transfer_container .= get_variable('lead_transfer_new_copy_status') . '|';
            $lead_transfer_container .= get_variable('lead_transfer_destination_list') . '|';
            $lead_transfer_container .= get_variable('lead_transfer_destination_status') . '|';

            if (get_variable('lead_transfer_action')=='COPY') {
                if (!empty($ret['container'])) $lead_transfer_container .= '|:' . $ret['container'];
            }

            $lxdl = get_variable('lead_transfer_destination_list');
            if (empty($lxdl)) {
                echo "<br><font color=red>LEAD TRANSFER POLICY RULE NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>lead transfer policy rule list must be selected</font><br>\n";
                $rule_error=1;
            }

        # Update rule
        } elseif ($SUB==3 or $SUB==4 or $SUB==5) {
            $ret=get_first_record($link, 'osdial_lead_transfers', '*', sprintf("id='%s'",mres($old_lead_transfer_id)));
            $lead_transfer_container = '';
            $rules = OSDpreg_split('/\|\:/',$ret['container']);
            if (is_array($rules) and $rules[0] != '') {
                $r=0;
                $newrules = array();
                $sortrules = array();
                foreach ($rules as $rule) {
                    if ($r == get_variable('rule')) {
                        $tmprule .= get_variable('lead_transfer_action') . '|';
                        $tmprule .= get_variable('lead_transfer_new_copy_status') . '|';
                        $tmprule .= get_variable('lead_transfer_destination_list') . '|';
                        $tmprule .= get_variable('lead_transfer_destination_status') . '|';

                        $rf = OSDpreg_split('/\|/',$rule);
                        $rconds = $rf[4];
                        $conds = array();
                        $rconds = OSDpreg_replace('/^\s+,/','',$rconds);
                        $rconds = OSDpreg_replace('/,\s+$/','',$rconds);
                        $rconds = OSDpreg_replace('/,\s+,/',',',$rconds);
                        $sconds = OSDpreg_split('/,/',$rconds);
                        if (is_array($sconds) and !empty($sconds[0])) {
                            foreach ($sconds as $cond) {
                                $ocond = OSDpreg_splitX('/\s+/',$cond,3);
                                if (!empty($ocond[0])) {
                                    $conds[$ocond[0]]['condition'] = $ocond[0];
                                    $conds[$ocond[0]]['operator'] = $ocond[1];
                                    $conds[$ocond[0]]['value'] = $ocond[2];
                                }
                            }
                        }

                        if ($SUB==4) {
                            $lxc = get_variable('lead_transfer_condition');
                            if (!array_key_exists($lxc,$conds)) {
                                $conds[$lxc]['condition'] = $lxc;
                                if ($lxc=='called_count') {
                                    $conds[$lxc]['operator'] = 'ge';
                                    $conds[$lxc]['value'] = '0';
                                } elseif ($lxc=='status') {
                                    $conds[$lxc]['operator'] = 'eq';
                                    $conds[$lxc]['value'] = '';
                                }
                            }
                        }

                        foreach (array('called_count'=>1,'status'=>1) as $k => $v) {
                            $lxch = get_variable('lead_transfer_condition_'.$k);
                            if (!empty($lxch)) {
                                $conds[$k]['condition'] = get_variable('lead_transfer_condition_'.$k);
                                $conds[$k]['operator'] = get_variable('lead_transfer_condition_'.$k.'_operator');
                                $conds[$k]['value'] = get_variable('lead_transfer_condition_'.$k.'_value');
                                if ($k=='called_count') {
                                    $conds[$k]['value'] = OSDpreg_replace('/\D*/','',$conds[$k]['value']) + 0;
                                }
                                if ($k=='status') {
                                    $conds[$k]['value'] = OSDpreg_replace('/^\s+/','',$conds[$k]['value']);
                                    $conds[$k]['value'] = OSDpreg_replace('/\s+$/','',$conds[$k]['value']);
                                    $stats = OSDpreg_split('/\s+/',$conds[$k]['value']);
                                    asort($stats);
                                    $stats = array_unique($stats);
                                    $conds[$k]['value'] = join('&', $stats);
                                }
                            }
                        }

                        if ($SUB==5) {
                            $lxcr = get_variable('lead_transfer_condition_remove');
                            if (!empty($lxcr) and array_key_exists($lxcr,$conds)) unset($conds[$lxcr]);
                        }

                        foreach ($conds as $cond) {
                            $tmprule .= $cond['condition'] . ' ' . $cond['operator'] . ' ' . $cond['value'] . ',';
                        }
                        $tmprule = OSDpreg_replace('/,$/','',$tmprule);
                        $rule = $tmprule;
                    }
                    $newrules[] = $rule;
                    $r++;
                }
                foreach (array('COPY','MOVE') as $saction) {
                    foreach ($newrules as $rule) {
                        $rf = OSDpreg_split('/\|/',$rule);
                        $raction = $rf[0];
                        if ($raction==$saction) {
                            $sortrules[] = $rule;
                        }
                    }
                }
                $lead_transfer_container = join('|:',$sortrules);
                $lead_transfer_container = OSDpreg_replace('/\|\:$/','',$lead_transfer_container);
            }

        # Delete rule
        } elseif ($SUB==6) {
            $ret=get_first_record($link, 'osdial_lead_transfers', '*', sprintf("id='%s'",mres($old_lead_transfer_id)));
            $lead_transfer_container = '';
            $rules = OSDpreg_split('/\|\:/',$ret['container']);
            if (is_array($rules) and $rules[0] != '') {
                $r=0;
                foreach ($rules as $rule) {
                    if ($r != get_variable('rule')) {
                        $lead_transfer_container .= $rule;
                        $lead_transfer_container .= '|:';
                    }
                    $r++;
                }
                $lead_transfer_container = OSDpreg_replace('/\|\:$/','',$lead_transfer_container);
            }
        }
        if ($rule_error==0) {
            if (OSDstrlen($lead_transfer_id) < 1) {
                echo "<br><font color=red>LEAD TRANSFER POLICY NOT MODIFIED - Please go back and look at the data you entered\n";
                echo "<br>lead transfer policy id must be at least 1 characters in length</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text>LEAD TRANSFER POLICY MODIFIED: $lead_transfer_id</font></b>\n";

                $stmt=sprintf("UPDATE osdial_lead_transfers SET id='%s',description='%s',active='%s',container='%s' WHERE id='%s';",mres($new_lead_transfer_id),mres($lead_transfer_description),mres($lead_transfer_active),mres($lead_transfer_container),mres($old_lead_transfer_id));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|MODIFY LEAD TRANSFER POLICY |$PHP_AUTH_USER|$ip|id='$new_lead_transfer_id',description='$lead_transfer_description',active='$lead_transfer_active',container='$lead_transfer_container' where id='$old_lead_transfer_id'|\n");
                    fclose($fp);
                }
            }
        }
        $ADD="31lx";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=31lx modify lead transfer rules
######################
if ($ADD=="31lx") {
    if ($LOG['modify_leads']==1 and $LOG['modify_lists']==1) {
        if ($LOG['multicomp']>0) {
            $lead_transfer_id = $LOG['company_prefix'] . $lead_transfer_id;
        }
        $lead_transfer_id=strtoupper($lead_transfer_id);
        $lx=get_first_record($link, 'osdial_lead_transfers', '*', sprintf("id='%s'",mres($lead_transfer_id)));
        echo "<center><br><font class=top_header color=$default_text size=+1>MODIFY LEAD TRANSFER POLICY RULES</font>".helptag("osdial_lead_transfers-rules")."<br><br>\n";
        echo "<table class=shadedtable width=$section_width cellspacing=3>\n";
        echo "  <tr bgcolor=$oddrows valign=middle>\n";
        echo "    <td align=center colspan=3><br>\n";
        echo "      <form action=$PHP_SELF method=post>\n";
        echo "      <input type=hidden name=ADD value=41lx>\n";
        echo "      <input type=hidden name=SUB value=0>\n";
        echo "      <input type=hidden name=old_lead_transfer_id value=\"".$lx['id']."\">\n";
        echo "      <input type=hidden name=lead_transfer_container value=\"".$lx['container']."\">\n";
        echo "      <table cellspacing=3 width=95%>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        $lxid=$lx['id'];
        if ($LOG['multicomp']>0) {
            $lxid = OSDsubstr($lxid,3,OSDstrlen($lxid));
        }
        echo "          <td align=left>ID: <input type=text name=lead_transfer_id size=20 maxlength=20 value=".$lxid."></td>\n";
        echo "          <td align=left>Description: <input type=text name=lead_transfer_description size=50 maxlength=255 value=".$lx['description']."></td>\n";
        $lx_nsel = $lx_ysel = "";
        if ($lead_transfer_active=="Y") {
            $lx_ysel = "selected";
        } else {
            $lx_nsel = "selected";
        }
        echo "          <td align=right>Active: <select size=1 name=lead_transfer_active><option $lx_ysel>Y</option><option $lx_nsel>N</option></select></td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>";
        echo "          <td colspan=3 align=center>";
        echo "            <input type=submit name=SUBMIT value=SUBMIT>";
        echo "          </td>";
        echo "        </tr>\n";
        echo "      </table>\n";
        echo "      </form>\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr class=tabheader>\n";
        echo "    <td>METHOD</td>\n";
        echo "    <td>DESTINATION</td>\n";
        echo "    <td>ACTIONS</td>\n";
        echo "  </tr>\n";

        $status_names='';
        $status_options='';
        function get_statuses_by_list($link,$list_id,$sel='',$bold='') {
            global $status_names;
            global $status_options;
            $status_names=array();
            $status_options='';
            $list_campaign_id='';
            $bold_statuses = OSDpreg_split('/\s+/','BLAHBLAH '.$bold);
            $ret=get_first_record($link, 'osdial_lists', '*', sprintf("list_id='%s'",mres($list_id)));
            if ($ret) $list_campaign_id=$ret['campaign_id'];
            $statuses = get_krh($link, 'osdial_campaign_statuses', '*','status ASC',sprintf("campaign_id='%s'",mres($list_campaign_id)),'');
            if (is_array($statuses)) {
                foreach ($statuses as $status) {
                    if (!array_key_exists($status['status'],$status_names)) {
                        if ($status['status'] != 'CALLBK') {
                            $ssel=''; if ($sel==$status['status']) $ssel='selected';
                            $status_label = $status['status']." - ".$status['status_name'];
                            if (array_search($status['status'],$bold_statuses)) $status_label = "--".$status_label;
                            $status_options .= "<option value=\"".$status['status']."\" ".$ssel.">".$status_label."</option>";
                        }
                        $status_names[$status['status']] = $status['status_name'];
                    }
                }
            }
            $statuses = get_krh($link, 'osdial_statuses', '*','status ASC','','');
            if (is_array($statuses)) {
                foreach ($statuses as $status) {
                    if (!array_key_exists($status['status'],$status_names)) {
                        if ($status['status'] != 'CALLBK') {
                            $ssel=''; if ($sel==$status['status']) $ssel='selected';
                            $status_label = $status['status']." - ".$status['status_name'];
                            if (array_search($status['status'],$bold_statuses)) $status_label = "--".$status_label;
                            $status_options .= "<option value=\"".$status['status']."\" ".$ssel.">".$status_label."</option>";
                        }
                        $status_names[$status['status']] = $status['status_name'];
                    }
                }
            }
        }

        $rules = OSDpreg_split('/\|\:/',$lx['container']);
        if (is_array($rules) and $rules[0] != '') {
            $r=0;
            foreach ($rules as $rule) {
                $rf = OSDpreg_split('/\|/',$rule);
                $raction = $rf[0];
                $rorig_new_status = $rf[1];
                $rdest_list = $rf[2];
                $rdest_status = $rf[3];
                $rconds = $rf[4];
                $rconds = OSDpreg_replace('/^\s+,/','',$rconds);
                $rconds = OSDpreg_replace('/,\s+$/','',$rconds);
                $rconds = OSDpreg_replace('/,\s+,/',',',$rconds);
                echo "  <form action=$PHP_SELF method=post name=lxr$r>\n";
                echo "  <input type=hidden name=ADD value=41lx>\n";
                echo "  <input type=hidden name=SUB value=3>\n";
                echo "  <input type=hidden name=rule value=$r>\n";
                echo "  <input type=hidden name=lead_transfer_condition_remove value=\"\">\n";
                echo "  <input type=hidden name=lead_transfer_id value=\"".$lxid."\">\n";
                echo "  <input type=hidden name=old_lead_transfer_id value=\"".$lx['id']."\">\n";
                echo "  <input type=hidden name=lead_transfer_description value=\"".$lx['description']."\">\n";
                echo "  <input type=hidden name=lead_transfer_active value=\"".$lx['active']."\">\n";
                echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
                echo "    <td align=left class=tabinput colspan=3>Rule #: ".($r+1)."</td>\n";
                echo "  </tr>\n";
                echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
                echo "    <td align=center class=tabinput>\n";
                $amsel=""; $acsel=""; if ($raction=='MOVE') $amsel='selected'; if ($raction=="COPY") $acsel='selected';
                echo "      <select size=1 name=lead_transfer_action onchange=\"if (this.selectedIndex==0) { document.lxr$r.lead_transfer_new_copy_status.selectedIndex=0; document.lxr$r.lead_transfer_new_copy_status.disabled=true; } else { document.lxr$r.lead_transfer_new_copy_status.disabled=false; }\">\n";
                echo "        <option $amsel>MOVE</option>\n";
                echo "        <option $acsel>COPY</option>\n";
                echo "      </select>\n";
                $ncsdisable=""; if ($raction=='MOVE') $ncsdisable='disabled';
                echo "      <select size=1 name=lead_transfer_new_copy_status $ncsdisable>\n";
                echo "        <option value=\"\">[OLD LEAD KEEPS STATUS]</option>\n";
                get_statuses_by_list($link,'',$rorig_new_status);
                echo $status_options;
                echo "      </select>\n";
                echo "    </td>\n";
                echo "    <td align=center class=tabinput>\n";
                echo "      <select size=1 name=lead_transfer_destination_list>\n";
                echo "        <option value=\"\" disabled>--SELECT LIST--</option>\n";
                $lists = list_id_list($link);
                if (is_array($lists)) {
                    foreach ($lists as $k => $v) {
                        $lsel=''; if ($k==$rdest_list) { $lsel='selected'; }
                        echo "        <option value=\"$k\" $lsel>$k - ".$v[0]."</option>\n";
                    }
                }
                echo "      </select>\n";
                echo "      <select size=1 name=lead_transfer_destination_status>\n";
                echo "        <option value=\"\">[NEW LEAD KEEPS STATUS]</option>\n";
                get_statuses_by_list($link,$rdest_list,$rdest_status);
                echo $status_options;
                echo "      </select>\n";
                echo "    </td>\n";

                $conds = array();
                $sconds = OSDpreg_split('/,/',$rconds);
                if (is_array($sconds) and !empty($sconds[0])) {
                    foreach ($sconds as $cond) {
                        $ocond = OSDpreg_splitX('/\s+/',$cond,3);
                        if (!empty($ocond[0])) {
                            $conds[$ocond[0]]['condition'] = $ocond[0];
                            $conds[$ocond[0]]['operator'] = $ocond[1];
                            $conds[$ocond[0]]['value'] = $ocond[2];
                            if ($ocond[0]=='status') {
                                $stats = OSDpreg_split('/\&/',$conds[$ocond[0]]['value']);
                                asort($stats);
                                $stats = array_unique($stats);
                                $conds[$ocond[0]]['value'] = join(' ', $stats);
                            }
                        }
                    }
                }

                $ccdisable=''; if (array_key_exists('called_count',$conds)) $ccdisable='disabled';
                $stdisable=''; if (array_key_exists('status',$conds)) $stdisable='disabled';
                echo "    <td align=center class=tabinput><select size=1 name=lead_transfer_condition onchange=\"if (document.lxr$r.lead_transfer_condition.selectedIndex==1) { document.lxr$r.SUB.value=4;document.lxr$r.submit(); } else if (document.lxr$r.lead_transfer_condition.selectedIndex==2) {document.lxr$r.SUB.value=4;document.lxr$r.submit(); }\"><option value=\"\">ADD CONDITION</option><option $ccdisable>called_count</option><option $stdisable>status</option></select></td>\n";
                echo "  </tr>\n";
                if (empty($rconds)) {
                    echo "  <tr " . bgcolor($o+1) . " class=\"row font1\">\n";
                    echo "    <td align=center class=tabinput colspan=4>\n";
                    echo "      <font color=red>You must add at least 1 condition.</font>\n";
                    echo "    </td>\n";
                    echo "  </tr>\n";
                } else {
                    ksort($conds);
                    foreach ($conds as $k => $v) {
                        echo "  <tr " . bgcolor($o+1) . " class=\"row font1\">\n";
                        echo "    <td align=left class=tabinput colspan=2>\n";
                        if ($k=='called_count') {
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Called Count &gt;=: ";
                            echo "<input type=hidden name=lead_transfer_condition_".$k." value=\"".$v['condition']."\">" ;
                            echo "<input type=hidden name=lead_transfer_condition_".$k."_operator value=\"".$v['operator']."\">" ;
                            echo "<input type=textfield name=lead_transfer_condition_".$k."_value size=5 value=\"".$v['value']."\">" ;
                        } elseif ($k=='status') {
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Statuses: ";
                            echo "<input type=hidden name=lead_transfer_condition_".$k." value=\"".$v['condition']."\">" ;
                            echo "<input type=hidden name=lead_transfer_condition_".$k."_operator value=\"".$v['operator']."\">" ;
                            echo "<input type=textfield name=lead_transfer_condition_".$k."_value size=70 value=\"".$v['value']."\" readonly>" ;
                            echo "      <select size=1 name=lead_transfer_condition_status_status onchange=\"lxopts=document.lxr$r.lead_transfer_condition_status_status.options[document.lxr$r.lead_transfer_condition_status_status.selectedIndex]; if (lxopts.label.match('^--')) {var re = new RegExp('^'+lxopts.value+'$|'+lxopts.value+'\\\\s+|\\\\s+'+lxopts.value,'');document.lxr$r.lead_transfer_condition_".$k."_value.value=document.lxr$r.lead_transfer_condition_".$k."_value.value.replace(re,''); } else { document.lxr$r.lead_transfer_condition_".$k."_value.value=document.lxr$r.lead_transfer_condition_".$k."_value.value+' '+document.lxr$r.lead_transfer_condition_status_status.value; } document.lxr$r.SUB.value=4;document.lxr$r.submit();\">\n";
                            echo "        <option value=\"\">ADD OR REMOVE STATUSES</option>\n";
                            get_statuses_by_list($link,'','',$v['value']);
                            echo $status_options;
                            echo "      </select>\n";
                        }
                        echo "    </td>\n";
                        echo "    <td align=center class=tabinput><input type=button name=lead_transfer_condition_remove_button onclick=\"document.lxr$r.SUB.value=5;document.lxr$r.lead_transfer_condition_remove.value='$k';document.lxr$r.submit();\" value=\"REMOVE CONDITION\"></td>\n";
                        echo "  </tr>\n";
                    }
                }
                echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
                echo "    <td align=center colspan=2>&nbsp;</td>\n";
                echo "    <td align=center class=tabinput><input type=submit value=UPDATE onclick=\"document.lxr$r.SUB.value=3;\"> | <input type=button value=DELETE onclick=\"document.lxr$r.SUB.value=6;document.lxr$r.submit();\"></td>\n";
                echo "  </tr>\n";
                echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabinput colspan=3></td>\n";
                echo "  </tr>\n";
                echo "  </form>\n";
                $r++;
            }
        }

        echo "  <form action=$PHP_SELF method=post name=lxr>\n";
        echo "  <input type=hidden name=ADD value=41lx>\n";
        echo "  <input type=hidden name=SUB value=2>\n";
        echo "  <input type=hidden name=lead_transfer_id value=\"".$lxid."\">\n";
        echo "  <input type=hidden name=old_lead_transfer_id value=\"".$lx['id']."\">\n";
        echo "  <input type=hidden name=lead_transfer_description value=\"".$lx['description']."\">\n";
        echo "  <input type=hidden name=lead_transfer_active value=\"".$lx['active']."\">\n";
        echo "  <tr class=tabfooter>\n";
        echo "    <td align=center class=tabinput>\n";
        echo "      <select size=1 name=lead_transfer_action onchange=\"if (this.selectedIndex==0) { document.lxr.lead_transfer_new_copy_status.selectedIndex=0; document.lxr.lead_transfer_new_copy_status.disabled=true; } else { document.lxr.lead_transfer_new_copy_status.disabled=false; }\">\n";
        echo "        <option>MOVE</option>\n";
        echo "        <option>COPY</option>\n";
        echo "      </select>\n";
        echo "      <select size=1 name=lead_transfer_new_copy_status disabled>\n";
        echo "        <option value=\"\">[OLD LEAD KEEPS STATUS]</option>\n";
        get_statuses_by_list($link,'','','');
        echo $status_options;
        echo "      </select>\n";
        echo "    </td>\n";
        echo "    <td align=center class=tabinput>\n";
        echo "      <select size=1 name=lead_transfer_destination_list>\n";
        echo "        <option value=\"\">--SELECT LIST--</option>\n";
        $lists = list_id_list($link);
        if (is_array($lists)) {
            foreach ($lists as $k => $v) {
                echo "        <option value=\"$k\">$k - ".$v[0]."</option>\n";
            }
        }
        echo "      </select>\n";
        echo "      <select size=1 name=lead_transfer_destination_status>\n";
        echo "        <option value=\"\">[NEW LEAD KEEPS STATUS]</option>\n";
        echo $status_options;
        echo "      </select>\n";
        echo "    </td>\n";
        echo "    <td align=center class=tabinput><input type=submit value=NEW></td>\n";
        echo "  </tr>\n";
        echo "  </form>\n";
        echo "</table></form></center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=10lx display lead transfer policies
######################
if ($ADD=="10lx") {
    if ($LOG['modify_lists']==1 and $LOG['modify_leads']==1) {

        # Add new lead transfer container here.
        if ($SUB==2) {
            if (empty($lead_transfer_id)) {
                echo "<br/><font color=red><b>ERROR: Lead Transfer ID cannot be empty.</b></font>\n";
            } else {
                if ($LOG['multicomp']>0) {
                    $lead_transfer_id = $LOG['company_prefix'] . $lead_transfer_id;
                }
                $lead_transfer_id=strtoupper($lead_transfer_id);
                $ret=get_first_record($link, 'osdial_lead_transfers', 'count(*) AS cnt', sprintf("id='%s'",mres($lead_transfer_id)));
                if ($ret['cnt']==0) {
                    $stmt=sprintf("INSERT INTO osdial_lead_transfers SET id='%s',description='%s',active='%s';",mres($lead_transfer_id),mres($lead_transfer_description),mres($lead_transfer_active));
                    $rslt=mysql_query($stmt, $link);
                    echo "<br/><font color=$default_text><b>Added New Lead Transfer Policy: ".mclabel($lead_transfer_id)."</b></font>\n";
                } else {
                    echo "<br/><font color=red><b>ERROR: Lead Transfer Policy ".mclabel($lead_transfer_id)." Exists!</b></font>\n";
                }
            }
        }

        if ($SUB==3) {
            if (empty($lead_transfer_id)) {
                echo "<br/><font color=red><b>ERROR: New Lead Transfer ID cannot be empty.</b></font>\n";
            } elseif (empty($old_lead_transfer_id)) {
                echo "<br/><font color=red><b>ERROR: Old Lead Transfer ID cannot be empty.</b></font>\n";
            } else {
                if ($LOG['multicomp']>0) {
                    $lead_transfer_id = $LOG['company_prefix'] . $lead_transfer_id;
                }
                $lead_transfer_id=strtoupper($lead_transfer_id);
                $old_lead_transfer_id=strtoupper($old_lead_transfer_id);
                $ret=get_first_record($link, 'osdial_lead_transfers', 'count(*) AS cnt', sprintf("id='%s'",mres($lead_transfer_id)));
                if ($ret['cnt']>0) {
                    $stmt=sprintf("UPDATE osdial_lead_transfers SET id='%s',description='%s',active='%s' WHERE id='%s';",mres($lead_transfer_id),mres($lead_transfer_description),mres($lead_transfer_active),mres($old_lead_transfer_id));
                    $rslt=mysql_query($stmt, $link);
                    echo "<br/><font color=$default_text><b>Modified Lead Transfer Policy: ".mclabel($lead_transfer_id)."</b></font>\n";
                } else {
                    echo "<br/><font color=red><b>ERROR: Lead Transfer Policy ".mclabel($lead_transfer_id)." Does Not Exist!</b></font>\n";
                }
            }
        }

        if ($SUB==5) {
            if (empty($old_lead_transfer_id)) {
                echo "<br/><font color=red><b>ERROR: Old Lead Transfer ID cannot be empty.</b></font>\n";
            } else {
                $old_lead_transfer_id=strtoupper($old_lead_transfer_id);
                echo "<br/><font color=$default_text><b>LEAD TRANSFER POLICY DELETION CONFIRMATION: $old_lead_transfer_id</b>\n";
                echo "<br/><br/><a href=\"$PHP_SELF?ADD=10lx&SUB=6&old_lead_transfer_id=$old_lead_transfer_id&CoNfIrM=YES\">Click here to delete policy $old_lead_transfer_id</a></font><br/><br/><br/>\n";
            }
        }
        if ($SUB==6) {
            if (empty($old_lead_transfer_id)) {
                echo "<br/><font color=red><b>ERROR: Old Lead Transfer ID cannot be empty.</b></font>\n";
            } else {
                $old_lead_transfer_id=strtoupper($old_lead_transfer_id);
                $stmt=sprintf("DELETE FROM osdial_lead_transfers WHERE id='%s';",mres($old_lead_transfer_id));
                $rslt=mysql_query($stmt, $link);
                echo "<br><font color=$default_text><b>LEAD TRANSFER POLICY DELETED: $old_lead_transfer_id</b></font>\n";
            }
        }

        echo "<center><br><font class=top_header color=$default_text size=+1>LEAD TRANSFER POLICIES</font>".helptag("osdial_lead_transfers-policies")."<br><br>";
        echo "<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1 style=\"white-space:nowrap;\">\n";
        echo "  <tr class=tabheader>\n";
        echo "    <td>ID</td>\n";
        echo "    <td>DESCRIPTION</td>\n";
        echo "    <td>ACTIVE</td>\n";
        echo "    <td>ACTIONS</td>\n";
        echo "  </tr>\n";

        $lxwhere='';
        if ($LOG['multicomp']>0) {
            $lxwhere=sprintf("id LIKE '%s%%'", $LOG['company_prefix']);
        }
        $lxs = get_krh($link, 'osdial_lead_transfers', '*','id ASC',$lxwhere,'');
        if (is_array($lxs)) {
            $o=0;
            foreach ($lxs as $lx) {
                $lx_nsel = $lx_ysel = "";
                if ($lx['active']=="Y") {
                    $lx_ysel = "selected";
                } else {
                    $lx_nsel = "selected";
                }
                echo "  <form action=$PHP_SELF method=POST name=lx$o>\n";
                echo "  <input type=hidden name=ADD value=10lx>\n";
                echo "  <input type=hidden name=SUB value=3>\n";
                echo "  <input type=hidden name=old_lead_transfer_id value=".$lx['id'].">\n";
                echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
                $lxid=$lx['id'];
                if ($LOG['multicomp']>0) {
                    $lxid = OSDsubstr($lxid,3,OSDstrlen($lxid));
                }
                $lxid=strtoupper($lxid);
                echo "    <td align=center class=tabinput><input type=text name=lead_transfer_id size=20 maxlength=20 value=\"" . $lxid . "\"></td>\n";
                echo "    <td align=center class=tabinput><input type=text name=lead_transfer_description size=50 maxlength=255 value=\"".$lx['description']."\"></td>\n";
                echo "    <td align=center class=tabinput><select size=1 name=lead_transfer_active><option $lx_nsel>N</option><option $lx_ysel>Y</option></select></td>\n";
                echo "    <td align=center class=tabinput><input type=submit value=\"UPDATE\" onclick=\"document.lx$o.SUB.value=3;\"> | <input type=button value=\"DELETE\" onclick=\"document.lx$o.SUB.value=6;document.lx$o.submit();\"> | <input type=button value=\"EDIT POLICY\" onclick=\"document.lx$o.ADD.value='31lx';document.lx$o.SUB.value='';document.lx$o.submit();\"></td>\n";
                echo "  </tr>\n";
                echo "  </form>\n";
                $o++;
            }
        }
        echo "  <form action=$PHP_SELF method=POST name=lxnew>\n";
        echo "  <input type=hidden name=ADD value=10lx>\n";
        echo "  <input type=hidden name=SUB value=2>\n";
        echo "  <tr class=tabfooter>\n";
        echo "    <td align=center class=tabinput><input type=text name=lead_transfer_id size=20 maxlength=20></td>\n";
        echo "    <td align=center class=tabinput><input type=text name=lead_transfer_description size=50 maxlength=255></td>\n";
        echo "    <td align=center class=tabinput><select size=1 name=lead_transfer_active><option>N</option><option>Y</option></select></td>\n";
        echo "    <td align=center class=tabbutton1><input type=submit name=submit value=ADD></td>\n";
        echo "  </tr>\n";
        echo "  </form>\n";

        echo "</table>\n";
        echo "</center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

?>
