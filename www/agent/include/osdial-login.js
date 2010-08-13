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
// getXHR() - Returns an xmlhttprequest or MS equiv.
	var getXHR = function () {
		var xmlhttp=false;
		try {
			xmlhttp = new XMLHttpRequest();
			if (xmlhttp) getXHR = function() { return new XMLHttpRequest(); };
		} catch(e) {
			var msxml = ['MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP', 'Microsoft.XMLHTTP'];
			for (var i=0, len = msxml.length; i < len; ++i) {
				try {
					xmlhttp = new ActiveXObject(msxml[i]);
					if (xmlhttp) {
						if (i==0) {
							getXHR = function() { return new ActiveXObject('MSXML2.XMLHTTP.3.0'); };
						} else if (i==1) {
							getXHR = function() { return new ActiveXObject('MSXML2.XMLHTTP'); };
						} else if (i==2) {
							getXHR = function() { return new ActiveXObject('Microsoft.XMLHTTP'); };
						}
					}
					break;
				} catch(e) {}
			}
		}
		return xmlhttp;
	}



// ################################################################################
// Send Request for allowable campaigns to populate the campaigns pull-down
    function login_allowable_campaigns() {
        var xmlhttp=getXHR();
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

