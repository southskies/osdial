<?php
#
# Copyright (C) 2013 Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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

function ShowCompanyPurchases() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }
    $html='';

    $comp['acct_startdate'] = OSDsubstr($comp['acct_startdate'],0,10);
    if ($comp['acct_startdate']=='0000-00-00') $comp['acct_startdate']='';
    $comp['acct_enddate'] = OSDsubstr($comp['acct_enddate'],0,10);
    if ($comp['acct_enddate']=='0000-00-00') $comp['acct_enddate']='';
    $html.="        <tr class=tabheader><td colspan=2>Accounting and Billing</td></tr>\n";
    $html.="        <tr bgcolor=$oddrows>\n";
    $html.="          <td align=right>Accounting Method:</td>\n";
    $html.="          <td align=left>\n";
    $html.="            <select size=1 name=acct_method>\n";
    $html.="              <option value=\"NONE\">NONE</option>\n";
    $html.="              <option value=\"RANGE\">RANGE</option>\n";
    $html.="              <option value=\"TOTAL\">TOTAL</option>\n";
    $html.="              <option value=\"AVAILABLE\">AVAILABLE</option>\n";
    $html.="              <option value=\"TALK\">TALK</option>\n";
    $html.="              <option value=\"TALK_ROUNDUP\">TALK_ROUNDUP</option>\n";
    $html.="              <option selected value=\"$comp[acct_method]\">" . $comp['acct_method'] . "</option>\n";
    $html.="            </select>\n";
    $html.="            ".helptag("companies-acct_method")."\n";
    $html.="          </td>\n";
    $html.="        </tr>\n";
    $html.="        <tr bgcolor=$oddrows>\n";
    $html.="          <td align=right>Start Date:</td>\n";
    $html.="          <td align=left><input type=text name=acct_startdate size=10 maxlength=10 value=\"$comp[acct_startdate]\">";
    $html.="      <a href=# onclick=\"cal1.enableClearDate();cal1.addDisabledDates('clear','clear');if (document.forms[0].acct_enddate.value!=null && document.forms[0].acct_enddate.value!='' && document.forms[0].acct_enddate.value!='0000-00-00') cal1.addDisabledDates(formatDate(parseDate(document.forms[0].acct_enddate.value).addDays(1),'yyyy-MM-dd'),null);cal1.select(document.forms[0].acct_startdate,'acal1','yyyy-MM-dd'); return false;\" name=acal1 id=acal1>\n";
    $html.="      <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
    $html .= helptag("companies-acct_startdate")."</td>\n";
    $html.="        </tr>\n";
    $html.="        <tr bgcolor=$oddrows>\n";
    $html.="          <td align=right>End Date:</td>\n";
    $html.="          <td align=left><input type=text name=acct_enddate size=10 maxlength=10 value=\"$comp[acct_enddate]\">";
    $html.="      <a href=# onclick=\"cal1.enableClearDate();cal1.addDisabledDates('clear','clear');if (document.forms[0].acct_startdate.value!=null && document.forms[0].acct_startdate.value!='' && document.forms[0].acct_startdate.value!='0000-00-00') cal1.addDisabledDates(null,formatDate(parseDate(document.forms[0].acct_startdate.value).addDays(-1),'yyyy-MM-dd'));cal1.select(document.forms[0].acct_enddate,'acal2','yyyy-MM-dd'); return false;\" name=acal2 id=acal2>\n";
    $html.="      <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
    $html .= helptag("companies-acct_enddate")."</td>\n";
    $html.="        </tr>\n";
    $html.="        <tr bgcolor=$oddrows>\n";
    $html.="          <td align=right>Cutoff Time:</td>\n";
    $html.="          <td align=left><input type=text name=acct_cutoff size=10 maxlength=15 value=\"$comp[acct_cutoff]\">".helptag("companies-acct_cutoff")."</td>\n";
    $html.="        </tr>\n";
    $html.="        <tr bgcolor=$oddrows>\n";
    $html.="          <td align=right>Expire Days:</td>\n";
    $html.="          <td align=left><input type=text name=acct_expire_days size=10 maxlength=15 value=\"$comp[acct_expire_days]\">".helptag("companies-acct_expire_days")."</td>\n";
    $html.="        </tr>\n";

    $html.="<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
    $html.="</table></form>\n";
    $html.="<div id=\"caldiv1\" style=\"position:absolute;visibility:hidden;background-color:white;\"></div>\n";

    $html.="<br><br>\n";

    if ($comp['acct_method'] != 'NONE' and $comp['acct_method'] != '') {

        $expire_date = '0000-00-00';
        #if ($comp['acct_method'] != 'RANGE') {
            if ($comp['acct_expire_days'] != '0') {
                $expire_date = date('Y-m-d');
                $tdate = strtotime($expire_date);
                $tdate = strtotime("+".$comp['acct_expire_days']." day", $tdate);
                $expire_date = date('Y-m-d',$tdate);
            }
        #} else {
            if ($comp['acct_expire_days'] == '0') $comp['acct_expire_days']='30';
            $Denddate = strtotime($comp['acct_enddate']);
            if ($comp['acct_enddate']=='0000-00-00 00:00:00') $Denddate=strtotime(date('Y-m-d'));
            if ($Denddate < strtotime(date('Y-m-d'))) $Denddate = strtotime(date('Y-m-d'));
            $Denddate_date = date('Y-m-d',$Denddate);
            $tdate = strtotime($Denddate_date);
            $tdate = strtotime("+".$comp['acct_expire_days']." day", $tdate);
            $Dexpire_date = date('Y-m-d',$tdate);
        #}
        if ($expire_date=='0000-00-00') $expire_date='';
        $payment_date = date('Y-m-d H:i:s');
        $html.= "<script type=\"text/javascript\">\n";
        $html.= "var lasttype='';\n";
        $html.= "</script>\n";
        $html.="<font class=top_header color=$default_text size=+1>PURCHASE AND PAYMENT ENTRY</font><br>";
        $html.="<form action=$PHP_SELF method=POST name=payment>";
        $html.="<input type=hidden name=ADD value=41comp>";
        $html.="<input type=hidden name=SUB value=purch>";
        $html.="<input type=hidden name=company_id value=$comp[id]>\n";
        $html.="<table class=shadedtable width=$section_width cellspacing=3>\n";
        $html.="        <tr bgcolor=$oddrows style=\"$bstyle\">\n";
        $html.="          <td align=right>Type:</td>\n";
        $html.="          <td align=left>\n";
        $mjs="document.getElementById('purchase_expire_date_label').innerHTML='Expire Date:'; document.getElementById('purchase_val_label').innerHTML='Value:'; document.getElementById('purchase_val_value').innerHTML='<input type=text name=purchase_val size=40 maxlength=255>';document.payment.purchase_expire_date.value='$expire_date';";
        $rjs="document.getElementById('purchase_expire_date_label').innerHTML='New End Date:'; document.getElementById('purchase_val_label').innerHTML='Last End Date:'; document.getElementById('purchase_val_value').innerHTML='<font size=2><b>$Denddate_date</b></font>'; var tdate = new Date('$Denddate_date'); tdate.setTime(tdate.getTime()+(1000*60*60*24*parseFloat('".$comp['acct_expire_days']."'))); document.payment.purchase_expire_date.value=tdate.toISOString().substr(0,10);";
        $packs = get_krh($link, 'osdial_acct_packages', '*','',sprintf("ptype!='OTHER' AND active='Y'"),'');
        $stype=" onchange=\"if(lasttype!=document.payment.purchase_type.value) document.payment.purchase_quantity.value='1'; lasttype=document.payment.purchase_type.value; fixChromeTableExpand2('QTY'); if (this.value=='DATE' || this.value=='MINUTES') {var actforms=document.getElementsByName('QTY'); for(var i2=0;i2<actforms.length;i2++) {actforms[i2].style.visibility='collapse';}} else {var actforms=document.getElementsByName('QTY');for(var i=0;i<actforms.length;i++) {actforms[i].style.visibility='visible';}}; fixChromeTableCollapse(); if (this.value!='DATE' ";
        $etype='';
        foreach ($packs as $pck) {
            if ($pck['ptype']=='DAYS') $stype.= " && this.value!='$pck[code]'";
            if ($pck['ptype']=='MINUTES') {
                $etype.="if(this.value=='$pck[code]') { if (document.payment.purchase_quantity.value==null || document.payment.purchase_quantity.value=='' || document.payment.purchase_quantity.value=='0') document.payment.purchase_quantity.value='1'; var bcnum=(".($pck[base_cost]*1)."*parseFloat(document.payment.purchase_quantity.value)); document.payment.payment_amount.value=bcnum.toFixed(2).toString(); document.payment.purchase_val.value='$pck[units]'; document.payment.purchase_expire_date.value='$expire_date';}; ";
            } elseif ($pck['ptype']=='DAYS') {
                $etype.="if(this.value=='$pck[code]') { if (document.payment.purchase_quantity.value==null || document.payment.purchase_quantity.value=='' || document.payment.purchase_quantity.value=='0') document.payment.purchase_quantity.value='1'; var bcnum=(".($pck[base_cost]*1)."*parseFloat(document.payment.purchase_quantity.value)); document.payment.payment_amount.value=bcnum.toFixed(2).toString(); var tdate = new Date('$Denddate_date'); tdate.setTime(tdate.getTime()+(1000*60*60*24*".($pck['units']*1)."*parseFloat(document.payment.purchase_quantity.value))); document.payment.purchase_expire_date.value=tdate.toISOString().substr(0,10);}; ";
            }
        }
        $stype.=" ) { $mjs } else { $rjs }; $etype\"";
        $html.="            <select size=1 name=purchase_type $stype>\n";
        if ($comp['acct_method'] == 'RANGE') {
            #$rtype='disabled';
        } else {
            #$mtype='disabled';
        }
        $html.="              <option value=\"DATE\" $mtype>DATE</option>\n";
        $html.="              <option value=\"MINUTES\" $rtype>MINUTES</option>\n";
        foreach ($packs as $pck) {
            $ttype=$mtype;
            if ($pck['ptype']=='MINUTES') $ttype=$rtype;
            $html.="              <option value=\"$pck[code]\" $ttype>$pck[code]: $pck[name]</option>\n";
        }
        $html.="            </select>\n";
        $html.="          </td>\n";
        $html.="        </tr>\n";
        $html.="        <tr bgcolor=$oddrows name=QTY style=\"visibility:collapse;\">\n";
        $html.="          <td align=right>Quantity:</td>\n";
        $html.="          <td align=left><input type=text name=purchase_quantity size=10 maxlength=255 value=\"1\" onchange=\"document.payment.purchase_type.onchange();\"></td>\n";
        $html.="        </tr>\n";
        $html.="        <tr bgcolor=$oddrows>\n";
        if ($comp['acct_method'] != 'RANGE') {
            $html.="          <td align=right id=purchase_val_label>Value:</td>\n";
            $html.="          <td align=left id=purchase_val_value><input type=text name=purchase_val size=40 maxlength=255 value=\"\"></td>\n";
        } else {
            $html.="          <td align=right id=purchase_val_label>Last End Date:</td>\n";
            $html.="          <td align=left id=purchase_val_value><font size=2><b>$Denddate_date</b></font></td>\n";
        }
        $html.="        </tr>\n";
        $html.="        <tr bgcolor=$oddrows>\n";
        if ($comp['acct_method'] != 'RANGE') {
            $html.="          <td align=right id=purchase_expire_date_label>Expire Date:</td>\n";
            $html.="          <td align=left><input type=text name=purchase_expire_date size=10 maxlength=10 value=\"$expire_date\">";
        } else {
            $html.="          <td align=right id=purchase_expire_date_label>New End Date:</td>\n";
            $html.="          <td align=left><input type=text name=purchase_expire_date size=10 maxlength=10 value=\"$Dexpire_date\">";
        }
        $html.="      <a href=# onclick=\"cal1.enableClearDate();cal1.addDisabledDates('clear','clear');cal1.addDisabledDates(null,formatDate(new Date(),'yyyy-MM-dd'));cal1.select(document.forms[1].purchase_expire_date,'acal3','yyyy-MM-dd'); return false;\" name=acal3 id=acal3>\n";
        $html.="      <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
        $html.="          </td>\n";
        $html.="        </tr>\n";
        $html.="        <tr bgcolor=$oddrows>\n";
        $html.="          <td align=right>Payment Method:</td>\n";
        $html.="          <td align=left><input type=text name=payment_method size=20 maxlength=20 value=\"\"></td>\n";
        $html.="        </tr>\n";
        $html.="        <tr bgcolor=$oddrows>\n";
        $html.="          <td align=right>Payment Type:</td>\n";
        $html.="          <td align=left><input type=text name=payment_type size=20 maxlength=20 value=\"\"></td>\n";
        $html.="        </tr>\n";
        $html.="        <tr bgcolor=$oddrows>\n";
        $html.="          <td align=right>Payment Amount:</td>\n";
        $html.="          <td align=left><input type=text name=payment_amount size=20 maxlength=20 value=\"0.00\"></td>\n";
        $html.="        </tr>\n";
        $html.="        <tr bgcolor=$oddrows>\n";
        $html.="          <td align=right>Payment TransID:</td>\n";
        $html.="          <td align=left><input type=text name=payment_transid size=20 maxlength=255 value=\"\"></td>\n";
        $html.="        </tr>\n";
        $html.="        <tr bgcolor=$oddrows>\n";
        $html.="          <td align=right>Payment Date:</td>\n";
        $html.="          <td align=left><input type=text name=payment_date size=19 maxlength=19 value=\"$payment_date\"></td>\n";
        $html.="        </tr>\n";
        $html.="<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        $html.="</table></form>\n";
        $html.= "<script type=\"text/javascript\">\n";
        $html.= "document.payment.purchase_type.onchange();\n";
        $html.= "fixChromeTableCollapse();\n";
        $html.= "</script>\n";

        $html.="<br><br>\n";

        $html.="<font class=top_header color=$default_text size=+1>PURCHASE AND PAYMENT HISTORY</font><br>\n";
        $html.="<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
        $html.="  <tr class=tabheader>";
        $html.="    <td width=5%>ID</td>\n";
        $html.="    <td width=5%>TRANSID</td>\n";
        $html.="    <td width=10%>TYPE</td>\n";
        $html.="    <td width=5%>QTY</td>\n";
        $html.="    <td width=14%>VALUE</td>\n";
        $html.="    <td width=13%>PMETHOD</td>\n";
        $html.="    <td width=13%>PTYPE</td>\n";
        $html.="    <td width=10%>PAMOUNT</td>\n";
        $html.="    <td width=12%>PDATE</td>\n";
        $html.="    <td width=12%>DATE</td>\n";
        $html.="  </tr>\n";
        $c=0;
        $purchs = get_krh($link, 'osdial_acct_purchases', '*','',sprintf("company_id='%s'",$comp['id']),'');
        foreach ($purchs as $purch) {
            $html.="  <tr " . bgcolor($c++) . " class=\"row font1\">\n";
            $html.="    <td>$purch[id]</td>\n";
            $html.="    <td align=right>$purch[trans_id]</td>\n";
            $html.="    <td align=center>$purch[purchase_type]</td>\n";
            $html.="    <td align=right>$purch[purchase_quantity]</td>\n";
            $html.="    <td align=right>$purch[purchase_val]</td>\n";
            $html.="    <td align=center>$purch[payment_method]</td>\n";
            $html.="    <td align=center>$purch[payment_type]</td>\n";
            $html.="    <td align=right>$purch[payment_amount]</td>\n";
            $html.="    <td align=right>$purch[payment_date]</td>\n";
            $html.="    <td align=right>$purch[created]</td>\n";
            $html.="  </tr>\n";
        }
        $html.="</table>\n";

    }

    return $html;
}

function AddUpdateCompanyPurchases() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }
    $html='';

    if ($SUB=='purch') {
        $html.="<br><font color=$default_text>COMPANY PURCHASE ADDED: $company_id : $company_name</font>\n";

        if ($purchase_expire_date == '' or $purchase_expire_date == '0') $purchase_expire_date = '0000-00-00';
        if ($purchase_expire_date == '0000-00-00') {
            $purchase_expire_date .= ' 00:00:00';
        } else {
            $purchase_expire_date .= ' 23:59:59';
        }

        $trans_id=0;
        if ($purchase_type=="MINUTES") {
            $trans_sec=($purchase_val * 1) * 60;

            $stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',expire_date='%s',created=NOW();",mres($company_id),mres('CREDIT'),mres($trans_sec),mres($purchase_expire_date));
            $rslt=mysql_query($stmt, $link);
            $trans_id =  mysql_insert_id($link);

            $stmt=sprintf("INSERT INTO osdial_acct_trans_daily SET company_id='%s',trans_type='%s',trans_sec='%s',created=NOW();",mres($company_id),mres('CREDIT'),mres($trans_sec));
            $rslt=mysql_query($stmt, $link);
        } elseif ($purchase_type=="DATE") {
            $purchase_val=OSDsubstr($purchase_expire_date,0,10);

            $stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',expire_date='%s',created=NOW();",mres($company_id),mres('DATE'),mres('0'),mres($purchase_expire_date));
            $rslt=mysql_query($stmt, $link);
            $trans_id =  mysql_insert_id($link);

            $stmt=sprintf("UPDATE osdial_companies SET acct_enddate='%s' WHERE id='%s';",mres($purchase_expire_date),mres($company_id));
            $rslt=mysql_query($stmt, $link);
        } else {
            $pck = get_first_record($link, 'osdial_acct_packages', '*', sprintf("code='%s'",mres($purchase_type)) );
            if ($pck['ptype']=='MINUTES') {
                $trans_sec=($purchase_val * 1) * 60;

                $stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',expire_date='%s',created=NOW();",mres($company_id),mres('CREDIT'),mres($trans_sec),mres($purchase_expire_date));
                $rslt=mysql_query($stmt, $link);
                $trans_id =  mysql_insert_id($link);

                $stmt=sprintf("INSERT INTO osdial_acct_trans_daily SET company_id='%s',trans_type='%s',trans_sec='%s',created=NOW();",mres($company_id),mres('CREDIT'),mres($trans_sec));
                $rslt=mysql_query($stmt, $link);

            } elseif ($pck['ptype']=='DAYS') {
                $purchase_val=OSDsubstr($purchase_expire_date,0,10);

                $stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',expire_date='%s',created=NOW();",mres($company_id),mres('DATE'),mres('0'),mres($purchase_expire_date));
                $rslt=mysql_query($stmt, $link);
                $trans_id =  mysql_insert_id($link);

                $stmt=sprintf("UPDATE osdial_companies SET acct_enddate='%s' WHERE id='%s';",mres($purchase_expire_date),mres($company_id));
                $rslt=mysql_query($stmt, $link);
            }
        }

        $stmt=sprintf("INSERT INTO osdial_acct_purchases SET company_id='%s',trans_id='%s',purchase_type='%s',purchase_val='%s',purchase_expire_date='%s',payment_method='%s',payment_type='%s',payment_amount='%s',payment_transid='%s',payment_date='%s',created=NOW();",mres($company_id),mres($trans_id),mres($purchase_type),mres($purchase_val),mres($purchase_expire_date),mres($payment_method),mres($payment_type),mres($payment_amount),mres($payment_transid),mres($payment_date));
        $rslt=mysql_query($stmt, $link);
    }

    return $html;
}

?>
