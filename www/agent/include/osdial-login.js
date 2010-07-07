/*
 * #
 * # Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
 * # Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
 * # Copyright (C) 2009  Steve Szmidt <techs@callcentersg.com>  LICENSE: AGPLv3
 * #
 * #     This file is part of OSDial.
 * #
 * #     OSDial is free software: you can redistribute it and/or modify
 * #     it under the terms of the GNU Affero General Public License as
 * #     published by the Free Software Foundation, either version 3 of
 * #     the License, or (at your option) any later version.
 * #
 * #     OSDial is distributed in the hope that it will be useful,
 * #     but WITHOUT ANY WARRANTY; without even the implied warranty of
 * #     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * #     GNU Affero General Public License for more details.
 * #
 * #     You should have received a copy of the GNU Affero General Public
 * #     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
 * #
 * #
 */






// ################################################################################
// Send Request for allowable campaigns to populate the campaigns pull-down
    function login_allowable_campaigns() {
        var xmlhttp=false;
        /*@cc_on @*/
        /*@if (@_jscript_version >= 5)
        // JScript gives us Conditional compilation, we can cope with old IE versions.
        // and security blocked creation of the objects.
        try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
        try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
        xmlhttp = false;
        }
        }
        @end @*/
        if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
            xmlhttp = new XMLHttpRequest();
        }
        if (xmlhttp) { 
            logincampaign_query = "&user=" + document.osdial_form.VD_login.value + "&pass=" + document.osdial_form.VD_pass.value + "&ACTION=LogiNCamPaigns&format=html";
            xmlhttp.open('POST', 'vdc_db_query.php'); 
            xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
            xmlhttp.send(logincampaign_query); 
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    Nactiveext = null;
                    Nactiveext = xmlhttp.responseText;
                    //	alert(logincampaign_query);
                    //	alert(xmlhttp.responseText);
                    document.getElementById("LogiNCamPaigns").innerHTML = Nactiveext;
                    document.getElementById("LogiNReseT").innerHTML = "<INPUT TYPE=BUTTON VALUE=\"Refresh Campaign List\" OnClick=\"login_allowable_campaigns()\">";
                    document.getElementById("VD_campaign").focus();
                    }
                }
                delete xmlhttp;
            }
        }

        function login_focus() {
            document.osdial_form.VD_campaign.blur();
            document.osdial_form.VD_login.focus();
            document.osdial_form.VD_campaign.onfocus=function(){login_allowable_campaigns();};
        }

        function login_submit() {
            document.getElementById("WelcomeBoxStatus").innerHTML = 'Authenticating...<br>&nbsp;<br>&nbsp;';
            document.getElementById("WelcomeBoxA").style.visibility = 'visible';
            document.osdial_form.submit();
        }

