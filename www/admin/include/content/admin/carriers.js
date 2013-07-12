// Carrier Templates

var dialplan =
	"; The following tags can be used to substitute corresponding values from the carrier options.\n"+
	";   <NAME>  <PROTOCOL>  <STRIP_MSD>  <ALLOW_INTERNATIONAL>\n"+
	";   <DEFAULT_CALLERID>  <DEFAULT_AREACODE>  <DEFAULT_PREFIX>\n"+
	"\n"+
	"\n"+
	"; Format long distance number (add default prefix).\n"+
	"exten => _1NXXNXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, 1 missing)\n"+
	"exten => _NNXXNXXXXXX,1,Goto(${EXTEN:0:1}1${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix, add 1).\n"+
	"exten => _NXXNXXXXXX,1,Goto(<DEFAULT_PREFIX>1${EXTEN},1)\n"+
	"\n"+
	"; Format local number (add 1, add default areacode)\n"+
	"exten => _XNXXXXXX,1,Goto(${EXTEN:0:1}1<DEFAULT_AREACODE>${EXTEN:1},1)\n"+
	"\n"+
	"; Format local number (add default prefix, add 1, add default areacode).\n"+
	"exten => _NXXXXXX,1,Goto(<DEFAULT_PREFIX>1<DEFAULT_AREACODE>${EXTEN},1)\n"+
	"\n"+
	"; Format international number (add default prefix).\n"+
	"exten => _011.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _00.,1,Goto(<DEFAULT_PREFIX>011${EXTEN:2},1)\n"+
	"\n"+
	"\n"+
	"; Dial long distance number (format correct).\n"+
	"exten => _X1NXXNXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}${EXTEN:2},1:setcid${EXTEN},1)\n"+
	"\n"+
	"; Dial an international number (if allowed).\n"+
	"exten => _X011.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _X00.,1,Goto(${EXTEN:0:1}011${EXTEN:3},1)\n"+
	"\n"+
	"\n"+
	"; Make sure callerid is set.\n"+
	"exten => _setcid.,1,GotoIf($[\"${LEN(${CALLERID(number)})}\" = \"${LEN(<DEFAULT_CALLERID>)}\"]?dial${EXTEN:6},1)\n"+
	"exten => _setcid.,n,Set(CALLERID(number)=<DEFAULT_CALLERID>)\n"+
	"exten => _setcid.,n,Goto(dial${EXTEN:6},1)\n"+
	"\n"+
	"\n"+
	"; This section is the 'prefix' dialed.\n"+
	"exten => _dial9.,1,AGI(agi://127.0.0.1:4577/call_log)\n"+
	"exten => _dial9.,n,Dial(<PROTOCOL>/<NAME>/${EXTEN:5},60,o)\n"+
	"exten => _dial9.,n,Goto(failover${EXTEN:5},1)\n";


var carriers = Array(
    "Generic - SIP","genericSIP",
	"[genericSIP]\n"+
        "username=USER\n"+
        "secret=PASSWORD\n"+
        "host=HOST\n"+
        "type=friend\n"+
        "dtmfmode=auto\n"+
        "qualify=yes\n"+
        "trunk=yes\n",
            "",
                dialplan,
    "Generic - IAX2","genericIAX",
	"[genericIAX]\n"+
        "username=USER\n"+
        "secret=PASSWORD\n"+
        "host=HOST\n"+
        "type=friend\n"+
        "qualify=yes\n",
            "",
                dialplan,
    "Binfone - SIP","binfoneSIP",
	"[binfoneSIP]\n"+
        "username=USER\n"+
        "secret=PASSWORD\n"+
        "type=friend\n"+
        "host=sip.binfone.com\n"+
        "dtmfmode=auto\n"+
        "qualify=yes\n"+
        "trunk=yes\n",
            "USER:PASSWORD@sip.binfone.com/USER",
                dialplan,
    "Binfone - IAX2","binfoneIAX",
	"[binfoneIAX]\n"+
        "username=USER\n"+
        "secret=PASSWORD\n"+
        "type=friend\n"+
        "host=iax-2.binfone.com\n"+
        "qualify=yes\n",
            "USER:PASSWORD@iax-2.binfone.com",
                dialplan,
    "XcastLabs","xcast",
	"[xcast]\n"+
        "host=38.102.250.50\n"+
        "type=friend\n"+
        "dtmfmode=auto\n"+
        "qualify=yes\n"+
        "trunk=yes\n"+
	"\n"+
	"[xcastIN]\n"+
        "host=38.102.250.60\n"+
        "type=friend\n"+
        "dtmfmode=auto\n"+
        "qualify=yes\n"+
        "trunk=yes\n",
            "",
                dialplan,
    "Vitelity","vitelity",
	"[vitelity]\n"+
        "host=outbound1.vitelity.net\n"+
        "type=friend\n"+
        "dtmfmode=auto\n"+
        "qualify=yes\n"+
        "trunk=yes\n"+
	"\n"+
	"[vitelityIN]\n"+
        "username=USER\n"+
        "secret=PASSWORD\n"+
        "host=inbound18.vitelity.net\n"+
        "type=friend\n"+
        "dtmfmode=auto\n"+
        "trustrpid=yes\n"+
        "sendrpid=yes\n"+
        "insecure=invite,port\n"+
        "qualify=yes\n"+
        "trunk=yes\n",
            "USER:PASSWORD@inbound18.vitelity.net:5060/USER",
                dialplan,
    "Airespring","airespring",
	"[airespring]\n"+
        "type=friend\n"+
        "host=64.211.41.115\n"+
        "trustrpid=yes\n"+
        "sendrpid=yes\n"+
        "disallow=all\n"+
        "allow=ulaw\n"+
        "dtmfmode=auto\n"+
        "qualify=yes\n"+
        "trunk=yes\n",
            "",
                dialplan,
    "Cordia","cordia",
	"[cordia]\n"+
        "username=USER\n"+
        "secret=PASSWORD\n"+
        "host=64.211.94.211\n"+
        "type=friend\n"+
        "dtmfmode=auto\n"+
        "canreinvite=yes\n"+
        "qualify=yes\n"+
        "trunk=yes\n",
            "",
                dialplan
);


if (document.osdial_form.cpt) {
    var tmp = document.osdial_form.cpt;
    for (var i=0; i<carriers.length; i++) {
        var label  = carriers[i++];
        var name   = carriers[i++];
        var config = carriers[i++];
        var reg    = carriers[i++];
        var dial   = carriers[i];
        tmp.options[tmp.length] = new Option(label,name + ':;:' + config + ':;:' + reg + ':;:' + dial);
    }
}

function selcarrier(sb) {
	if (sb.selectedIndex>0) {
		var opts = sb.options[sb.selectedIndex].value.split(':;:');
		document.getElementById('carrier_description').value = sb.options[sb.selectedIndex].text;
		document.getElementById('carrier_name').value = opts[0];
		document.getElementById('carrier_protocol_config').value = 
			"; Do not add a 'context' directive.\n"+
			"; The name of your carrier MUST match the name in the first set of square-brackets '[]'.\n"+
			opts[1];
		document.getElementById('carrier_registrations').value = opts[2];
		document.getElementById('carrier_dialplan').value = opts[3];
		if (sb.selectedIndex == 2 || sb.selectedIndex == 4) {
			document.getElementById('carrier_protocol').selectedIndex = 1;
		} else {
			document.getElementById('carrier_protocol').selectedIndex = 0;
		}
	}
}

function selaction(sb) {
	for (var i=0; i<sb.options.length; i++) {
		var actforms = document.getElementsByName(sb.options[i].text);
		for (var i2=0; i2<actforms.length; i2++) {
			actforms[i2].style.visibility = 'collapse';
		}
	}

	var actname = sb.options[sb.selectedIndex].text;
	var actforms = document.getElementsByName(actname);
	for (var i=0; i<actforms.length; i++) {
		actforms[i].style.visibility = 'visible';
	}
}

function updateingroup(sb) {
	document.osdial_form.did_ingroup.options[0].text = ' [ CREATE NEW INGROUP: '+document.osdial_form.company.value+'IN_' + sb.value + ' ] ';
}
