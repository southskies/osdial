<?php
### report_acct_detail.php
### 
#
# Copyright (C) 2013  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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

function report_acct_detail() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $reporttype = get_variable('reporttype');
    $agent_log_id = get_variable('agent_log_id');
    $credit_trans_id = get_variable('credit_trans_id');
    $trans_types = get_variable('trans_types');
    $company_prefix = get_variable('company_prefix');
    $begin_date = get_variable('begin_date');
    $end_date = get_variable('end_date');
    $submit = get_variable('submit');
    $SUBMIT = get_variable('SUBMIT');
    $DB = get_variable('DB');
    
    $STARTtime = date("U");
    $TODAY = date("Y-m-d");
    
    $html='';
    $head='';
    $table='';

    if ($company_prefix) $company = ($company_prefix * 1) - 100;
    if ($LOG['multicomp_user'] > 0) {
        $company = $LOG['company']['id'];
    }

    if ($reporttype=='') $reporttype='SUMMARY';
    
    if ($begin_date == "") {$begin_date = $TODAY;}
    if ($end_date == "") {$end_date = $TODAY;}
    
    $head .= "<br>\n";
    $head .= "<center><font class=top_header color=$default_text size=4>ACCOUNTING DETAIL</font></center><br>\n";
    if ($company) {
        $stmt=sprintf("SELECT name,status,acct_method FROM osdial_companies WHERE id='%s';",mres($company));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $company_name = $row[0];
        $company_status = $row[1];
        $acct_method = $row[2];

        $head .= "<center><font color=$default_text size=3><b>".(($company*1)+100)." - $company_name</b></font></center><br/>\n";
    }

    $tran_credit='';
    $tran_debit='';
    $tran_adjustment='';
    $tran_expired='';
    $tran_date='';
    $transSQL='';
    foreach ($trans_types as $tran) {
        $transSQL.="'$tran',";
        if ($tran=='CREDIT') $tran_credit=' selected';
        if ($tran=='DEBIT') $tran_debit=' selected';
        if ($tran=='ADJUSTMENT') $tran_adjustment=' selected';
        if ($tran=='EXPIRED') $tran_expired=' selected';
        if ($tran=='DATE') $tran_date=' selected';
    }
    $transSQL = rtrim($transSQL, ',');
    if ($transSQL=='') {
        if ($acct_method=='RANGE') {
            $tran_date=' selected';
            $transSQL="'DATE'";
        } else if (OSDpreg_match('/^TOTAL$|^AVAILABLE$|^TALK$|^TALK_ROUNDUP$/',$acct_method)) {
            $tran_credit=' selected';
            $tran_debit=' selected';
            $tran_adjustment=' selected';
            $tran_expired=' selected';
            $transSQL="'CREDIT','DEBIT','ADJUSTMENT','EXPIRED'";
        } else if ($acct_method=='NONE' or $acct_method=='') {
            $transSQL="''";
        }
    }
    $rptsel_trans='';
    $rptsel_credrec='';
    $rptsel_loganal='';
    if ($reporttype=='TRANS') $rptsel_trans=' selected';
    if ($reporttype=='CREDREC') $rptsel_credrec=' selected';
    if ($reporttype=='SUMMARY') $rptsel_summary=' selected';
    $footcolspan=2;
    $head .= "<form name=range action=$PHP_SELF method=POST>\n";
    $head .= "<input type=hidden name=ADD value=\"$ADD\">\n";
    $head .= "<input type=hidden name=SUB value=\"$SUB\">\n";
    $head .= "<input type=hidden name=DB value=\"$DB\">\n";
    if ($LOG['multicomp_admin']>0) {
        $footcolspan++;
    } elseif ($LOG['multicomp']>0) {
        $head .= "<input type=hidden name=company_prefix value=$company_prefix>";
    } else {
        $head .= "<input type=hidden name=company_prefix value=\"\">";
    }
    $head .= "<table class=shadedtable align=center cellspacing=1 width=700 bgcolor=grey>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td colspan=$footcolspan align=center>Report:</td>\n";
    $head .= "  </tr>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td colspan=$footcolspan align=center>\n";
    $head .= "      <select size=1 name=reporttype onchange='document.range.submit.click();'>\n";
    $head .= "          <option value=\"SUMMARY\"$rptsel_summary>Summary</option>\n";
    $head .= "          <option value=\"TRANS\"$rptsel_trans>Transaction Selection</option>\n";
    $head .= "          <option value=\"CREDREC\"$rptsel_credrec>Credit Utilization</option>\n";
    $head .= "      </select>\n";
    $head .= "    </td>\n";
    $head .= "  </tr>\n";
    $head .= "  <tr class=tabheader>\n";
    if ($LOG['multicomp_admin'] > 0) {
        $head .= "    <td width='33%' style='vertical-align:-40px;'>";
    } else {
        $head .= "    <td width='50%' style='vertical-align:-40px;'>";
    }
    if ($reporttype=='TRANS') {
        $head .= "      Date Range";
    } else if ($reporttype=='CREDREC') {
        $head .= "      Credit Transaction ID";
    }
    $head .= "    </td>\n";
    if ($LOG['multicomp_admin'] > 0) {
        $head .= "    <td width='34%'>\n";
    } else {
        $head .= "    <td width='50%'>\n";
    }
    if ($reporttype=='TRANS') {
        $head .= "      Transaction Type";
    }
    $head .= "    </td>\n";
    if ($LOG['multicomp_admin'] > 0) {
        $head .= "    <td width='33%'>Company</td>\n";
    }
    $head .= "  </tr>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td style='vertical-align:top;'>\n";
    if ($reporttype=='TRANS') {
        $head .= "      <script>\nvar cal1 = new CalendarPopup('caldiv1');\ncal1.showNavigationDropdowns();\n</script>\n";
        $head .= "      <div style='display:inline-block;width:50px;'>Start:</div><input type=text name=begin_date value=\"$begin_date\" size=10 maxsize=10>\n";
        $head .= "      <a href=# onclick=\"cal1.addDisabledDates('clear','clear');cal1.addDisabledDates(formatDate(parseDate(document.forms[0].end_date.value).addDays(1),'yyyy-MM-dd'),null);cal1.select(document.forms[0].begin_date,'acal1','yyyy-MM-dd'); return false;\" name=acal1 id=acal1>\n";
        $head .= "      <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a><br/>\n";
        $head .= "      <div style='display:inline-block;width:50px;'>End:</div><input type=text name=end_date value=\"$end_date\" size=10 maxsize=10>\n";
        $head .= "      <a href=# onclick=\"cal1.addDisabledDates('clear','clear');cal1.addDisabledDates(null,formatDate(parseDate(document.forms[0].begin_date.value).addDays(-1),'yyyy-MM-dd'));cal1.select(document.forms[0].end_date,'acal2','yyyy-MM-dd'); return false;\" name=acal2 id=acal2>\n";
        $head .= "      <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
    } else if ($reporttype=='CREDREC') {
        $head .= "    <input name=\"credit_trans_id\" type=text size=15 maxsize=255 value=\"$credit_trans_id\">";
    }
    $head .= "    </td>\n";
    $head .= "    <td>\n";
    if ($reporttype=='TRANS') {
        $head .= "      <select size=6 multiple=1 name=\"trans_types[]\">\n";
        $head .= "          <option value=\"CREDIT\"$tran_credit>Credits</option>\n";
        $head .= "          <option value=\"DEBIT\"$tran_debit>Debits</option>\n";
        $head .= "          <option value=\"ADJUSTMENT\"$tran_adjustment>Adjustments</option>\n";
        $head .= "          <option value=\"EXPIRED\"$tran_expired>Expired</option>\n";
        $head .= "          <option value=\"DATE\"$tran_date>Date</option>\n";
        $head .= "      </select>\n";
    }
    $head .= "    </td>\n";
    if ($LOG['multicomp_admin'] > 0) {
        $head .= "    <td>\n";
        $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
        $copts = array();
        foreach ($comps as $comp) {
            $ckey = (($comp['id'] * 1) + 100);
            $clabel = (($comp['id'] * 1) + 100) . ": " . $comp['name'];
            $copts[$ckey] = $clabel;
        }
        $head .= "<font color=black>";
        $head .= editableSelectBox($copts,'company_prefix',$company_prefix,100,100,'selectBoxForce="1" selectBoxLabel=" - All Companies - "');
        $head .= "</font>";
        $head .= "    </td>\n";
    }
    $head .= "  </tr>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td colspan=$footcolspan class=tabbutton><input type=submit name=submit value=SUBMIT></td>\n";
    $head .= "  </tr>\n";
    $head .= "</table>\n";
    $head .= "</form>\n";
    $head .= "<div id=\"caldiv1\" style=\"position:absolute;visibility:hidden;background-color:white;\"></div>\n";
    
    if (!$LOG['view_reports']) {
        $table .= "<center><font color=red>You do not have permission to view this page</font></center>\n";
    } else {
        $query_date_BEGIN = "$begin_date 00:00:00";
        $query_date_BEGIN = dateToServer($link,'first',$query_date_BEGIN,$webClientAdjGMT,'',$webClientDST,0);
        $query_date_END = "$end_date 23:59:59";
        $query_date_END = dateToServer($link,'first',$query_date_END,$webClientAdjGMT,'',$webClientDST,0);

        
        $compSQL=sprintf("company_id='%s' AND",mres($company));
        $compSQL2=sprintf("id='%s' AND",mres($company));
        if ($LOG['multicomp_admin'] > 0) {
            if ($company==0 or $company=='') {
                $compSQL='';
                $compSQL2='';
            }
        }
    
        if ($reporttype=='SUMMARY') {
            $sumary=array();
            $stmt=sprintf("SELECT company_id,trans_type,count(*) AS tcnt,sum(trans_sec) AS tsum FROM osdial_acct_trans WHERE %s 1=1 GROUP BY company_id,trans_type;",$compSQL);
            $rslt=mysql_query($stmt, $link);
            $logs_to_print = mysql_num_rows($rslt);
            $u=0;
            while ($logs_to_print > $u) {
                $row=mysql_fetch_row($rslt);
                $u++;
                $sumary['C'.$row[0]][$row[1]]['COUNT'] = $row[2];
                $sumary['C'.$row[0]][$row[1]]['SUM'] = $row[3];
            }

            $table .= "<br><br>\n";
            $table .= "<center><font color=$default_text size=3><b>SUMMARY</b></font></center>\n";
            $table .= "<center>\n";
            $th=''; if ($logs_to_print>30) $th = "height:500px;";
            #$table .= "<div style=\"overflow:auto;width:770px;$th\">\n";
            $table .= "<table class=shadedtable align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey style=\"cursor:crosshair;\">\n";
            $table .= "  <tr class=tabheader>\n";
            $table .= "    <td>COMPANY</td>\n";
            $table .= "    <td>NAME</td>\n";
            $table .= "    <td>STATUS</td>\n";
            $table .= "    <td>METHOD</td>\n";
            $table .= "    <td>CREDIT</td>\n";
            $table .= "    <td>DEBIT</td>\n";
            $table .= "    <td>ADJUSTMENT</td>\n";
            $table .= "    <td>EXPIRATION</td>\n";
            $table .= "    <td>BALANCE</td>\n";
            $table .= "  </tr>\n";

            $u=0;
            $comps = get_krh($link, 'osdial_companies', '*','',$compSQL2.' 1=1','');
            foreach ($comps as $comp) {
                $u++;
                $compsum=0;
                $comptrans=0;
                $compsum+=($sumary['C'.$comp['id']]['CREDIT']['SUM']*1);
                $compsum+=($sumary['C'.$comp['id']]['DEBIT']['SUM']*1);
                $compsum+=($sumary['C'.$comp['id']]['ADJUSTMENT']['SUM']*1);
                $compsum+=($sumary['C'.$comp['id']]['EXPIRED']['SUM']*1);
                $comptrans+=($sumary['C'.$comp['id']]['CREDIT']['COUNT']*1);
                $comptrans+=($sumary['C'.$comp['id']]['DEBIT']['COUNT']*1);
                $comptrans+=($sumary['C'.$comp['id']]['ADJUSTMENT']['COUNT']*1);
                $comptrans+=($sumary['C'.$comp['id']]['EXPIRED']['COUNT']*1);
                if ($comp['acct_method']=='') $comp['acct_method']='NONE';
                $table .= "  <tr " . bgcolor($u) . " class=\"row font1\" style=\"white-space:nowrap;\">\n";
                $table .= "    <td align=right title=\"Company: ".(($comp['id']*1)+100)."\">".(($comp['id']*1)+100)."</td>\n";
                $table .= "    <td align=right title=\"Name: $comp[name]\">$comp[name]</td>\n";
                $table .= "    <td align=right title=\"Status: $comp[status]\">$comp[status]</td>\n";
                $table .= "    <td align=right title=\"Accounting Method: $comp[acct_method]\">$comp[acct_method]</td>\n";
                $table .= "    <td align=right title=\"Credits: ".($sumary['C'.$comp['id']]['CREDIT']['SUM']*1)." in ".($sumary['C'.$comp['id']]['CREDIT']['COUNT']*1)." transactions.\">".($sumary['C'.$comp['id']]['CREDIT']['SUM']*1)."</td>\n";
                $table .= "    <td align=right title=\"Debits: ".($sumary['C'.$comp['id']]['DEBIT']['SUM']*1)." in ".($sumary['C'.$comp['id']]['DEBIT']['COUNT']*1)." transactions.\">".($sumary['C'.$comp['id']]['DEBIT']['SUM']*1)."</td>\n";
                $table .= "    <td align=right title=\"Adjustments: ".($sumary['C'.$comp['id']]['ADJUSTMENT']['SUM']*1)." in ".($sumary['C'.$comp['id']]['ADJUSTMENT']['COUNT']*1)." transactions.\">".($sumary['C'.$comp['id']]['ADJUSTMENT']['SUM']*1)."</td>\n";
                $table .= "    <td align=right title=\"Expired: ".($sumary['C'.$comp['id']]['EXPIRED']['SUM']*1)." in ".($sumary['C'.$comp['id']]['EXPIRED']['COUNT']*1)." transactions.\">".($sumary['C'.$comp['id']]['EXPIRED']['SUM']*1)."</td>\n";
                $table .= "    <td align=right title=\"Balance: ".($compsum*1)." with ".($comptrans*1)." total transactions.\">".($compsum*1)."</td>\n";
                $table .= "  </tr>\n";
            }
            $table .= "  <tr class=tabfooter>\n";
            $table .= "    <td colspan=11></td>";
            $table .= "  </tr>\n";
            $table .= "</table>\n";
            #$table .= "</div>\n";
            $table .= "</center>\n";
        }

        if ($reporttype=='TRANS') {
            $stmt=sprintf("SELECT oat.id,company_id,oat.agent_log_id,trans_type,trans_sec,ref_id,reconciled,expire_date,updated,created,oal.event_time,oal.user,oal.user_group,oal.server_ip,oal.lead_id,oal.campaign_id,pause_sec,wait_sec,talk_sec,dispo_sec,status,uniqueid,comments FROM osdial_acct_trans AS oat LEFT JOIN osdial_agent_log AS oal ON oat.agent_log_id=oal.agent_log_id WHERE %s created BETWEEN '%s' AND '%s' AND trans_type IN (%s) ORDER BY id DESC;",$compSQL,mres($query_date_BEGIN),mres($query_date_END),$transSQL);
            $rslt=mysql_query($stmt, $link);
            $logs_to_print = mysql_num_rows($rslt);
        
            $table .= "<br><br>\n";
            $table .= "<center><font color=$default_text size=3><b>$transSQL TRANSACTIONS</b></font></center>\n";
            $table .= "<center>\n";
            $th=''; if ($logs_to_print>30) $th = "height:500px;";
            #$table .= "<div style=\"overflow:auto;width:770px;$th\">\n";
            $table .= "<table class=shadedtable align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey style=\"cursor:crosshair;\">\n";
            $table .= "  <tr class=tabheader>\n";
            $table .= "    <td># </td>\n";
            $table .= "    <td>ID</td>\n";
            $table .= "    <td>COMPANY</td>\n";
            $table .= "    <td>LOGID</td>\n";
            $table .= "    <td>TYPE</td>\n";
            $table .= "    <td>SEC</td>\n";
            $table .= "    <td>REFID</td>\n";
            $table .= "    <td>RECONCILED</td>\n";
            $table .= "    <td>EXPIRATION</td>\n";
            $table .= "    <td>UPDATED</td>\n";
            $table .= "    <td>CREATED</td>\n";
            $table .= "  </tr>\n";
        
            $totsec=0;
            $u=0;
            while ($logs_to_print > $u) {
                $row=mysql_fetch_row($rslt);
                $u++;
                $totsec=intval($row[4]) + $totsec;
                if ($row[6] == '0000-00-00 00:00:00') $row[6]='';
                if ($row[7] == '0000-00-00 00:00:00') $row[7]='';
                if ($row[8] == '0000-00-00 00:00:00') $row[8]='';
                if ($row[9] == '0000-00-00 00:00:00') $row[9]='';
                $reconciled = OSDpreg_replace("/ /", "&nbsp;", $row[6]);
                $expire_date = OSDpreg_replace("/ /", "&nbsp;", $row[7]);
                $updated = OSDpreg_replace("/ /", "&nbsp;", $row[8]);
                $created = OSDpreg_replace("/ /", "&nbsp;", $row[9]);
                $table .= "  <tr " . bgcolor($u) . " class=\"row font1\" style=\"white-space:nowrap;\">\n";
                $table .= "    <td align=left title=\"Record #: $u\">$u</td>\n";
                $table .= "    <td align=right title=\"Transaction ID: $row[0]\">$row[0]</td>\n";
                $table .= "    <td align=right title=\"Company ID: ".(($row[1]*1)+100)."\">".(($row[1]*1)+100)."</td>\n";

                $table .= "    <td align=right title=\"Agent Log ID: $row[2]\">";
                if ($row[2]>0) {
                    $table .= "<span class=\"helpcontainer\" style=\"position:relative;\"><span id=\"CUALINFO$row[2]\" class=\"helppopup helptranshidden\"><br><br><table onclick=\"event.stopPropagation();\" class=\"shadedroundtable maintable\" width=\"500\">";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Agent Log ID:</td><td align=\"left\"><b>$row[2]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Event Time:</td><td align=\"left\"><b>$row[10]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">User:</td><td align=\"left\"><b><a href=\"$PHP_SELF?ADD=999999&SUB=21&begin_date=".OSDsubstr($row[10],0,10)."&end_date=".OSDsubstr($row[10],0,10)."&agent=$row[11]\" target=\"_blank\">$row[11]</a></b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">User Group:</td><td align=\"left\"><b>$row[12]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Server IP:</td><td align=\"left\"><b>$row[13]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Lead ID:</td><td align=\"left\"><b><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[14]\" target=\"_blank\">$row[14]</a></b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Campaign ID:</td><td align=\"left\"><b>$row[15]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Pause:</td><td align=\"left\"><b>$row[16]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Wait:</td><td align=\"left\"><b>$row[17]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Talk:</td><td align=\"left\"><b>$row[18]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Dispo:</td><td align=\"left\"><b>$row[19]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Status:</td><td align=\"left\"><b>$row[20]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">UniqueID:</td><td align=\"left\"><b>$row[21]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Comments:</td><td align=\"left\"><b>$row[22]</b></td></tr>";
                    $table .= "</table><br><br></span></span>";
                    $table .= "<a href=\"javascript:var hp = document.getElementById('CUALINFO$row[2]');hp.style.backgroundColor='#BBB';hp.style.width='500px';hp.classList.remove('helptranshidden');hp.classList.add('helptransvisible');var closealog = function(){var hp = document.getElementById('CUALINFO$row[2]');hp.classList.remove('helptransvisible');hp.classList.add('helptranshidden');if(document.body.removeEventListener){document.body.removeEventListener('click',closealog,false);}else if(document.body.detachEvent){document.body.detachEvent('on'+'click',closealog);}};if (document.body.addEventListener) {document.body.addEventListener('click',closealog,false);} else if(document.body.attachEvent) {document.body.attachEvent('on'+'click',closealog);}\">";
                }
                $table .= "$row[2]</a></td>\n";

                $table .= "    <td align=right title=\"Transaction Type: $row[3]\">$row[3]</td>\n";
                $table .= "    <td align=right title=\"Transaction Time: $row[4]\">$row[4]</td>\n";
                if ($row[5]>0) {
                    $table .= "    <td align=right title=\"Reference ID: $row[5]\"><a href=\"$PHP_SELF?ADD=999999&SUB=32&reporttype=CREDREC&credit_trans_id=$row[5]\" target=\"_blank\">$row[5]</a></td>\n";
                } else {
                    $table .= "    <td align=right title=\"Reference ID: $row[5]\">$row[5]</td>\n";
                }
                $table .= "    <td align=center title=\"Reconciled Date/Time: $reconciled\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[6],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                $table .= "    <td align=center title=\"Expire Date/Time: $expire_date\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[7],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                $table .= "    <td align=center title=\"Updated Date/Time: $updated\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[8],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                $table .= "    <td align=center title=\"Created Date/Time: $created\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[9],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                $table .= "  </tr>\n";
            }
            $table .= "  <tr class=tabfooter>\n";
            $table .= "    <td colspan=5></td>";
            $table .= "    <td align=right colspan=1>".$totsec."</td>";
            $table .= "    <td align=center colspan=5>Minutes: ".($totsec/60)."</td>";
            $table .= "  </tr>\n";
            $table .= "</table>\n";
            #$table .= "</div>\n";
            $table .= "</center>\n";
        }

        if ($reporttype=='CREDREC') {
            $stmt=sprintf("SELECT oat.id,company_id,oat.agent_log_id,trans_type,trans_sec,ref_id,reconciled,expire_date,updated,created,oal.event_time,oal.user,oal.user_group,oal.server_ip,oal.lead_id,oal.campaign_id,pause_sec,wait_sec,talk_sec,dispo_sec,status,uniqueid,comments FROM osdial_acct_trans AS oat LEFT JOIN osdial_agent_log AS oal ON oat.agent_log_id=oal.agent_log_id WHERE %s trans_type='%s' ORDER BY oat.id ASC;",$compSQL,'CREDIT');
            $rslt=mysql_query($stmt, $link);
            $logs_to_print = mysql_num_rows($rslt);
        
            $table .= "<br><br>\n";
            $table .= "<center><font color=$default_text size=3><b>CREDIT UTILIZATION</b></font></center>\n";
            $table .= "<center>\n";
            $th=''; if ($logs_to_print>30) $th = "height:500px;";
            #$table .= "<div style=\"overflow:auto;width:770px;$th\">\n";
            $table .= "<table class=shadedtable align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey style=\"cursor:crosshair;\">\n";
            $table .= "  <tr class=tabheader>\n";
            $table .= "    <td># </td>\n";
            $table .= "    <td>ID</td>\n";
            $table .= "    <td>COMPANY</td>\n";
            $table .= "    <td>LOGID</td>\n";
            $table .= "    <td>TYPE</td>\n";
            $table .= "    <td>SEC</td>\n";
            $table .= "    <td>REFID</td>\n";
            $table .= "    <td>RECONCILED</td>\n";
            $table .= "    <td>EXPIRATION</td>\n";
            $table .= "    <td>UPDATED</td>\n";
            $table .= "    <td>CREATED</td>\n";
            $table .= "  </tr>\n";

            $totsec=0;
            $u=0;
            while ($logs_to_print > $u) {
                $row=mysql_fetch_row($rslt);
                $u++;
                $totsec=intval($row[4]) + $totsec;
                if ($row[6] == '0000-00-00 00:00:00') $row[6]='';
                if ($row[7] == '0000-00-00 00:00:00') $row[7]='';
                if ($row[8] == '0000-00-00 00:00:00') $row[8]='';
                if ($row[9] == '0000-00-00 00:00:00') $row[9]='';
                $reconciled = OSDpreg_replace("/ /", "&nbsp;", $row[6]);
                $expire_date = OSDpreg_replace("/ /", "&nbsp;", $row[7]);
                $updated = OSDpreg_replace("/ /", "&nbsp;", $row[8]);
                $created = OSDpreg_replace("/ /", "&nbsp;", $row[9]);
                $table .= "  <tr " . bgcolor($u) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=999999&SUB=32&reporttype=CREDREC&credit_trans_id=$row[0]';\" style=\"white-space:nowrap;\">\n";
                $table .= "    <td align=left title=\"Record #: $u\">$u</td>\n";
                $table .= "    <td align=right title=\"Transaction ID: $row[0]\"><a href=\"$PHP_SELF?ADD=999999&SUB=32&reporttype=CREDREC&credit_trans_id=$row[0]\">$row[0]</a></td>\n";
                $table .= "    <td align=right title=\"Company ID: ".(($row[1]*1)+100)."\">".(($row[1]*1)+100)."</td>\n";

                $table .= "    <td align=right title=\"Agent Log ID: $row[2]\">";
                if ($row[2]>0) {
                    $table .= "<span class=\"helpcontainer\" style=\"position:relative;\"><span id=\"CUALINFO$row[2]\" class=\"helppopup helptranshidden\"><br><br><table onclick=\"event.stopPropagation();\" class=\"shadedroundtable maintable\" width=\"500\">";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Agent Log ID:</td><td align=\"left\"><b>$row[2]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Event Time:</td><td align=\"left\"><b>$row[10]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">User:</td><td align=\"left\"><b><a href=\"$PHP_SELF?ADD=999999&SUB=21&begin_date=".OSDsubstr($row[10],0,10)."&end_date=".OSDsubstr($row[10],0,10)."&agent=$row[11]\" target=\"_blank\">$row[11]</a></b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">User Group:</td><td align=\"left\"><b>$row[12]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Server IP:</td><td align=\"left\"><b>$row[13]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Lead ID:</td><td align=\"left\"><b><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[14]\" target=\"_blank\">$row[14]</a></b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Campaign ID:</td><td align=\"left\"><b>$row[15]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Pause:</td><td align=\"left\"><b>$row[16]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Wait:</td><td align=\"left\"><b>$row[17]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Talk:</td><td align=\"left\"><b>$row[18]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Dispo:</td><td align=\"left\"><b>$row[19]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Status:</td><td align=\"left\"><b>$row[20]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">UniqueID:</td><td align=\"left\"><b>$row[21]</b></td></tr>";
                    $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Comments:</td><td align=\"left\"><b>$row[22]</b></td></tr>";
                    $table .= "</table><br><br></span></span>";
                    $table .= "<a href=\"javascript:var hp = document.getElementById('CUALINFO$row[2]');hp.style.backgroundColor='#BBB';hp.style.width='500px';hp.classList.remove('helptranshidden');hp.classList.add('helptransvisible');var closealog = function(){var hp = document.getElementById('CUALINFO$row[2]');hp.classList.remove('helptransvisible');hp.classList.add('helptranshidden');if(document.body.removeEventListener){document.body.removeEventListener('click',closealog,false);}else if(document.body.detachEvent){document.body.detachEvent('on'+'click',closealog);}};if (document.body.addEventListener) {document.body.addEventListener('click',closealog,false);} else if(document.body.attachEvent) {document.body.attachEvent('on'+'click',closealog);}\">";
                }
                $table .= "$row[2]</a></td>\n";

                $table .= "    <td align=right title=\"Transaction Type: $row[3]\">$row[3]</td>\n";
                $table .= "    <td align=right title=\"Transaction Time: $row[4]\">$row[4]</td>\n";
                $table .= "    <td align=right title=\"Reference ID: $row[5]\">$row[5]</td>\n";
                $table .= "    <td align=center title=\"Reconciled Date/Time: $reconciled\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[6],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                $table .= "    <td align=center title=\"Expire Date/Time: $expire_date\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[7],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                $table .= "    <td align=center title=\"Updated Date/Time: $updated\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[8],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                $table .= "    <td align=center title=\"Created Date/Time: $created\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[9],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                $table .= "  </tr>\n";
            }
            $table .= "  <tr class=tabfooter>\n";
            $table .= "    <td colspan=5></td>";
            $table .= "    <td align=right colspan=1>".$totsec."</td>";
            $table .= "    <td align=center colspan=5>Minutes: ".($totsec/60)."</td>";
            $table .= "  </tr>\n";
            $table .= "</table>\n";
            #$table .= "</div>\n";

            if ($credit_trans_id) {
                $stmt=sprintf("SELECT oat.id,company_id,oat.agent_log_id,trans_type,trans_sec,ref_id,reconciled,expire_date,updated,created,oal.event_time,oal.user,oal.user_group,oal.server_ip,oal.lead_id,oal.campaign_id,pause_sec,wait_sec,talk_sec,dispo_sec,status,uniqueid,comments FROM osdial_acct_trans AS oat LEFT JOIN osdial_agent_log AS oal ON oat.agent_log_id=oal.agent_log_id WHERE %s (oat.id='%s' OR ref_id='%s') ORDER BY oat.id ASC;",$compSQL,mres($credit_trans_id),mres($credit_trans_id));
                $rslt=mysql_query($stmt, $link);
                $logs_to_print = mysql_num_rows($rslt);
        
                $table .= "<br><br>\n";
                $table .= "<br><br>\n";
                $table .= "<center><font color=$default_text size=3><b>ALL TRANSACTIONS LINKED TO $credit_trans_id.</b></font></center>\n";
                $table .= "<center>\n";
                $th=''; if ($logs_to_print>30) $th = "height:500px;";
                #$table .= "<div style=\"overflow:auto;width:770px;$th\">\n";
                $table .= "<table class=shadedtable align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey style=\"cursor:crosshair;\">\n";
                $table .= "  <tr class=tabheader>\n";
                $table .= "    <td># </td>\n";
                $table .= "    <td>ID</td>\n";
                $table .= "    <td>COMPANY</td>\n";
                $table .= "    <td>LOGID</td>\n";
                $table .= "    <td>TYPE</td>\n";
                $table .= "    <td>SEC</td>\n";
                $table .= "    <td>REFID</td>\n";
                $table .= "    <td>RECONCILED</td>\n";
                $table .= "    <td>EXPIRATION</td>\n";
                $table .= "    <td>UPDATED</td>\n";
                $table .= "    <td>CREATED</td>\n";
                $table .= "  </tr>\n";

                $totsec=0;
                $u=0;
                while ($logs_to_print > $u) {
                    $row=mysql_fetch_row($rslt);
                    $u++;
                    $totsec=intval($row[4]) + $totsec;
                    if ($row[6] == '0000-00-00 00:00:00') $row[6]='';
                    if ($row[7] == '0000-00-00 00:00:00') $row[7]='';
                    if ($row[8] == '0000-00-00 00:00:00') $row[8]='';
                    if ($row[9] == '0000-00-00 00:00:00') $row[9]='';
                    $reconciled = OSDpreg_replace("/ /", "&nbsp;", $row[6]);
                    $expire_date = OSDpreg_replace("/ /", "&nbsp;", $row[7]);
                    $updated = OSDpreg_replace("/ /", "&nbsp;", $row[8]);
                    $created = OSDpreg_replace("/ /", "&nbsp;", $row[9]);
                    $table .= "  <tr " . bgcolor($u) . " class=\"row font1\" style=\"white-space:nowrap;\">\n";
                    $table .= "    <td align=left title=\"Record #: $u\">$u</td>\n";
                    $table .= "    <td align=right title=\"Transaction ID: $row[0]\">$row[0]</td>\n";
                    $table .= "    <td align=right title=\"Company ID: ".(($row[1]*1)+100)."\">".(($row[1]*1)+100)."</td>\n";
                    $table .= "    <td align=right title=\"Agent Log ID: $row[2]\">";
                    if ($row[2]>0) {
                        $table .= "<span class=\"helpcontainer\" style=\"position:relative;\"><span id=\"CUALINFO$row[2]\" class=\"helppopup helptranshidden\"><br><br><table onclick=\"event.stopPropagation();\" class=\"shadedroundtable maintable\" width=\"500\">";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Agent Log ID:</td><td align=\"left\"><b>$row[2]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Event Time:</td><td align=\"left\"><b>$row[10]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">User:</td><td align=\"left\"><b><a href=\"$PHP_SELF?ADD=999999&SUB=21&begin_date=".OSDsubstr($row[10],0,10)."&end_date=".OSDsubstr($row[10],0,10)."&agent=$row[11]\" target=\"_blank\">$row[11]</a></b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">User Group:</td><td align=\"left\"><b>$row[12]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Server IP:</td><td align=\"left\"><b>$row[13]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Lead ID:</td><td align=\"left\"><b><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[14]\" target=\"_blank\">$row[14]</a></b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Campaign ID:</td><td align=\"left\"><b>$row[15]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Pause:</td><td align=\"left\"><b>$row[16]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Wait:</td><td align=\"left\"><b>$row[17]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Talk:</td><td align=\"left\"><b>$row[18]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Dispo:</td><td align=\"left\"><b>$row[19]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Status:</td><td align=\"left\"><b>$row[20]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">UniqueID:</td><td align=\"left\"><b>$row[21]</b></td></tr>";
                        $table .= "<tr bgcolor=\"#DDD\"><td align=\"right\">Comments:</td><td align=\"left\"><b>$row[22]</b></td></tr>";
                        $table .= "</table><br><br></span></span>";
                        $table .= "<a href=\"javascript:var hp = document.getElementById('CUALINFO$row[2]');hp.style.backgroundColor='#BBB';hp.style.width='500px';hp.classList.remove('helptranshidden');hp.classList.add('helptransvisible');var closealog = function(){var hp = document.getElementById('CUALINFO$row[2]');hp.classList.remove('helptransvisible');hp.classList.add('helptranshidden');if(document.body.removeEventListener){document.body.removeEventListener('click',closealog,false);}else if(document.body.detachEvent){document.body.detachEvent('on'+'click',closealog);}};if (document.body.addEventListener) {document.body.addEventListener('click',closealog,false);} else if(document.body.attachEvent) {document.body.attachEvent('on'+'click',closealog);}\">";
                    }
                    $table .= "$row[2]</a></td>\n";
                    $table .= "    <td align=right title=\"Transaction Type: $row[3]\">$row[3]</td>\n";
                    $table .= "    <td align=right title=\"Transaction Time: $row[4]\">$row[4]</td>\n";
                    $table .= "    <td align=right title=\"Reference ID: $row[5]\">$row[5]</td>\n";
                    $table .= "    <td align=center title=\"Reconciled Date/Time: $reconciled\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[6],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                    $table .= "    <td align=center title=\"Expire Date/Time: $expire_date\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[7],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                    $table .= "    <td align=center title=\"Updated Date/Time: $updated\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[8],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                    $table .= "    <td align=center title=\"Created Date/Time: $created\">" . OSDpreg_replace("/ /","&nbsp;",dateToLocal($link,$config['VARserver_ip'],$row[9],$webClientAdjGMT,'',$webClientDST,1)) . "</td>\n";
                    $table .= "  </tr>\n";
                }
                $table .= "  <tr class=tabfooter>\n";
                $table .= "    <td colspan=5></td>";
                $table .= "    <td align=right colspan=1>".$totsec."</td>";
                $table .= "    <td align=center colspan=5>Minutes: ".($totsec/60)."</td>";
                $table .= "  </tr>\n";
                $table .= "</table>\n";
                #$table .= "</div>\n";
            }

        }



    }
        
    $ENDtime = date("U");
    $RUNtime = ($ENDtime - $STARTtime);
        
    $table .= "<br><br><br>\n";
    $table .= "<font size=0>\n";
    $table .= "  Script Runtime: $RUNtime seconds\n";
    $table .= "</font>\n";
        
    $html .= "<div class=noprint>$head</div>\n";
    $html .= "<div class=noprint>$table</div>\n";
        
    return $html;
        
}
        
?>
