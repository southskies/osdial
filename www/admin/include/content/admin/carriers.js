// Carrier Templates

var dialplan_key =
	"\n"+
	"; The following tags can be used to substitute corresponding values from the carrier options.\n"+
	";   <NAME>  <PROTOCOL>  <STRIP_MSD>  <ALLOW_INTERNATIONAL>\n"+
	";   <DEFAULT_CALLERID>  <DEFAULT_AREACODE>  <DEFAULT_PREFIX>\n"+
	"\n";

var dialplan_tail =
	"\n"+
	"; Make sure callerid is set.\n"+
	"exten => _setcid.,1,GotoIf($[\"${LEN(${CALLERID(number)})}\" = \"${LEN(<DEFAULT_CALLERID>)}\"]?dial${EXTEN:6},1)\n"+
	"exten => _setcid.,n,Set(CALLERID(number)=<DEFAULT_CALLERID>)\n"+
	"exten => _setcid.,n,Goto(dial${EXTEN:6},1)\n"+
	"\n"+
	"; This section is the 'prefix' dialed.\n"+
	"exten => _dial9.,1,AGI(agi://127.0.0.1:4577/call_log)\n"+
	"exten => _dial9.,n,Dial(<PROTOCOL>/<NAME>/${EXTEN:5},60,oR)\n"+
	"exten => _dial9.,n,Goto(failover${EXTEN:5},1)\n";

var dialplan_1 =
	"\n"+
	"; Dialplan Example for NANPA / USA / Canada / Mexico\n"+
	";---------------------------------------------------\n"+
	"; Country code: 1\n"+
	"; Number length: 10-digits\n"+
	"; International Prefix: 011\n"+
	"; Trunk Prefix: none\n"+
	";\n"+
	dialplan_key +
	"; Format long distance number (add default prefix).\n"+
	"exten => _1NXXNXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code)\n"+
	"exten => _NNXXNXXXXXX,1,Goto(${EXTEN:0:1}1${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix, add country code).\n"+
	"exten => _NXXNXXXXXX,1,Goto(<DEFAULT_PREFIX>1${EXTEN},1)\n"+
	"\n"+
	"; Format local number (prefix dialed, add country code, add default areacode)\n"+
	"exten => _NNXXXXXX,1,Goto(${EXTEN:0:1}1<DEFAULT_AREACODE>${EXTEN:1},1)\n"+
	"\n"+
	"; Format local number (add default prefix, add country code, add default areacode).\n"+
	"exten => _NXXXXXX,1,Goto(<DEFAULT_PREFIX>1<DEFAULT_AREACODE>${EXTEN},1)\n"+
	"\n"+
	"; Dial long distance number (format correct).\n"+
	"exten => _N1NXXNXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}${EXTEN:2},1:setcid${EXTEN},1)\n"+
	"\n"+
	"; Format international number (add default prefix).\n"+
	"exten => _011.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _00.,1,Goto(<DEFAULT_PREFIX>011${EXTEN:2},1)\n"+
	"\n"+
	"; Dial an international number (if allowed).\n"+
	"exten => _N011.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _N00.,1,Goto(${EXTEN:0:1}011${EXTEN:3},1)\n"+
	dialplan_tail;


var dialplan_44 =
	";\n"+
	"; Dialplan Example for UK\n"+
	";------------------------------\n"+
	"; Country code: 44\n"+
	"; Number length: 9 to 10-digits\n"+
	"; International Prefix: 00\n"+
	"; Trunk Prefix: 0\n"+
	";\n"+
	dialplan_key +
	"; Format long distance number (add default prefix, remove 0).\n"+
	"exten => _440ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:0:2}${EXTEN:3},1)\n"+
	"exten => _440ZXXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:0:2}${EXTEN:3},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix).\n"+
	"exten => _44ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _44ZXXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix, remove 0).\n"+
	"exten => _0ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>44${EXTEN:1},1)\n"+
	"exten => _0ZXXXXXXXXX,1,Goto(<DEFAULT_PREFIX>44${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code)\n"+
	"exten => _NZXXXXXXXX,1,Goto(${EXTEN:0:1}44${EXTEN:1},1)\n"+
	"exten => _NZXXXXXXXXX,1,Goto(${EXTEN:0:1}44${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code, remove 0)\n"+
	"exten => _N0ZXXXXXXXX,1,Goto(${EXTEN:0:1}44${EXTEN:2},1)\n"+
	"exten => _N0ZXXXXXXXXX,1,Goto(${EXTEN:0:1}44${EXTEN:2},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, remove 0)\n"+
	"exten => _N440ZXXXXXXXX,1,Goto(${EXTEN:0:1}${EXTEN:1:2}${EXTEN:4},1)\n"+
	"exten => _N440ZXXXXXXXXX,1,Goto(${EXTEN:0:1}${EXTEN:1:2}${EXTEN:4},1)\n"+
	"\n"+
	"; Format long distance number (format correct)\n"+
	"exten => _N44ZXXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}0${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"exten => _N44ZXXXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}0${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"\n"+
	"; Format international number (add default prefix).\n"+
	"exten => _00.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _011.,1,Goto(<DEFAULT_PREFIX>00${EXTEN:3},1)\n"+
	"\n"+
	"; Dial an international number (if allowed).\n"+
	"exten => _N00.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _N0044.,1,Goto(${EXTEN:0:1}${EXTEN:3},1)\n"+
	"exten => _N011.,1,Goto(${EXTEN:0:1}00${EXTEN:4},1)\n"+
	dialplan_tail;


var dialplan_46 =
	";\n"+
	"; Dialplan Example for Sweden\n"+
	";------------------------------\n"+
	"; Country code: 46\n"+
	"; Number length: 7 to 9-digits\n"+
	"; International Prefix: 00\n"+
	"; Trunk Prefix: 0\n"+
	";\n"+
	dialplan_key +
	"; Format long distance number (add default prefix, remove 0).\n"+
	"exten => _460ZXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:0:2}${EXTEN:3},1)\n"+
	"exten => _460ZXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:0:2}${EXTEN:3},1)\n"+
	"exten => _460ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:0:2}${EXTEN:3},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix).\n"+
	"exten => _46ZXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _46ZXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _46ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix, remove 0).\n"+
	"exten => _0ZXXXXXX,1,Goto(<DEFAULT_PREFIX>46${EXTEN:1},1)\n"+
	"exten => _0ZXXXXXXX,1,Goto(<DEFAULT_PREFIX>46${EXTEN:1},1)\n"+
	"exten => _0ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>46${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code)\n"+
	"exten => _NZXXXXXX,1,Goto(${EXTEN:0:1}46${EXTEN:1},1)\n"+
	"exten => _NZXXXXXXX,1,Goto(${EXTEN:0:1}46${EXTEN:1},1)\n"+
	"exten => _NZXXXXXXXX,1,Goto(${EXTEN:0:1}46${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code, remove 0)\n"+
	"exten => _N0ZXXXXXX,1,Goto(${EXTEN:0:1}46${EXTEN:2},1)\n"+
	"exten => _N0ZXXXXXXX,1,Goto(${EXTEN:0:1}46${EXTEN:2},1)\n"+
	"exten => _N0ZXXXXXXXX,1,Goto(${EXTEN:0:1}46${EXTEN:2},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, remove 0)\n"+
	"exten => _N460ZXXXXXX,1,Goto(${EXTEN:0:1}${EXTEN:1:2}${EXTEN:4},1)\n"+
	"exten => _N460ZXXXXXXX,1,Goto(${EXTEN:0:1}${EXTEN:1:2}${EXTEN:4},1)\n"+
	"exten => _N460ZXXXXXXXX,1,Goto(${EXTEN:0:1}${EXTEN:1:2}${EXTEN:4},1)\n"+
	"\n"+
	"; Format long distance number (format correct)\n"+
	"exten => _N46ZXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}0${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"exten => _N46ZXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}0${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"exten => _N46ZXXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}0${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"\n"+
	"; Format international number (add default prefix).\n"+
	"exten => _00.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _011.,1,Goto(<DEFAULT_PREFIX>00${EXTEN:3},1)\n"+
	"\n"+
	"; Dial an international number (if allowed).\n"+
	"exten => _N00.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _N0046.,1,Goto(${EXTEN:0:1}${EXTEN:3},1)\n"+
	"exten => _N011.,1,Goto(${EXTEN:0:1}00${EXTEN:4},1)\n"+
	dialplan_tail;


var dialplan_48 =
	";\n"+
	"; Dialplan Example for Poland\n"+
	";------------------------------\n"+
	"; Country code: 48\n"+
	"; Number length: 9-digits\n"+
	"; International Prefix: 00\n"+
	"; Trunk Prefix: none\n"+
	";\n"+
	dialplan_key +
	"; Format long distance number (add default prefix, add country code).\n"+
	"exten => _ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>48${EXTEN},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix).\n"+
	"exten => _48ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code)\n"+
	"exten => _NZXXXXXXXX,1,Goto(${EXTEN:0:1}48${EXTEN:1},1)\n"+
	"\n"+
	"; Dial long distance number (format correct).\n"+
	"exten => _N48ZXXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"\n"+
	"; Format international number (add default prefix).\n"+
	"exten => _00.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _011.,1,Goto(<DEFAULT_PREFIX>00${EXTEN:3},1)\n"+
	"\n"+
	"; Dial an international number (if allowed).\n"+
	"exten => _N00.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _N0048.,1,Goto(${EXTEN:0:1}${EXTEN:3},1)\n"+
	"exten => _N011.,1,Goto(${EXTEN:0:1}00${EXTEN:4},1)\n"+
	dialplan_tail;


var dialplan_61 =
	";\n"+
	"; Dialplan Example for Australia\n"+
	";--------------------------------\n"+
	"; Country code: 61\n"+
	"; Number length: 9-digits\n"+
	"; International Prefix: 0011\n"+
	"; Trunk Prefix: 0\n"+
	";\n"+
	dialplan_key +
	"; Format long distance number (add default prefix, remove 0).\n"+
	"exten => _610ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:0:2}${EXTEN:3},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix).\n"+
	"exten => _61ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix, remove 0).\n"+
	"exten => _0ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>61${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code)\n"+
	"exten => _NZXXXXXXXX,1,Goto(${EXTEN:0:1}61${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code, remove 0)\n"+
	"exten => _N0ZXXXXXXXX,1,Goto(${EXTEN:0:1}61${EXTEN:2},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, remove 0)\n"+
	"exten => _N610ZXXXXXXXX,1,Goto(${EXTEN:0:1}${EXTEN:1:2}${EXTEN:4},1)\n"+
	"\n"+
	"; Format long distance number (format correct)\n"+
	"exten => _N61ZXXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}0${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"\n"+
	"; Format international number (add default prefix).\n"+
	"exten => _0011.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _00N.,1,Goto(<DEFAULT_PREFIX>0011${EXTEN:2},1)\n"+
	"exten => _011.,1,Goto(<DEFAULT_PREFIX>0011${EXTEN:3},1)\n"+
	"\n"+
	"; Dial an international number (if allowed).\n"+
	"exten => _N0011.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _N001161.,1,Goto(${EXTEN:0:1}${EXTEN:5},1)\n"+
	"exten => _N00N.,1,Goto(${EXTEN:0:1}0011${EXTEN:3},1)\n"+
	"exten => _N011.,1,Goto(${EXTEN:0:1}0011${EXTEN:4},1)\n"+
	dialplan_tail;


var dialplan_64 =
	";\n"+
	"; Dialplan Example for New Zealand\n"+
	";----------------------------------\n"+
	"; Country code: 64\n"+
	"; Number length: 8 to 9-digits\n"+
	"; International Prefix: 00\n"+
	"; Trunk Prefix: 0\n"+
	";\n"+
	dialplan_key +
	"; Format long distance number (add default prefix, remove 0).\n"+
	"exten => _640ZXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:0:2}${EXTEN:3},1)\n"+
	"exten => _640ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:0:2}${EXTEN:3},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix).\n"+
	"exten => _64ZXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _64ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix, remove 0).\n"+
	"exten => _0ZXXXXXXX,1,Goto(<DEFAULT_PREFIX>64${EXTEN:1},1)\n"+
	"exten => _0ZXXXXXXXX,1,Goto(<DEFAULT_PREFIX>64${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code)\n"+
	"exten => _NZXXXXXXX,1,Goto(${EXTEN:0:1}64${EXTEN:1},1)\n"+
	"exten => _NZXXXXXXXX,1,Goto(${EXTEN:0:1}64${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code, remove 0)\n"+
	"exten => _N0ZXXXXXXX,1,Goto(${EXTEN:0:1}64${EXTEN:2},1)\n"+
	"exten => _N0ZXXXXXXXX,1,Goto(${EXTEN:0:1}64${EXTEN:2},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, remove 0)\n"+
	"exten => _N640ZXXXXXXX,1,Goto(${EXTEN:0:1}${EXTEN:1:2}${EXTEN:4},1)\n"+
	"exten => _N640ZXXXXXXXX,1,Goto(${EXTEN:0:1}${EXTEN:1:2}${EXTEN:4},1)\n"+
	"\n"+
	"; Format long distance number (format correct)\n"+
	"exten => _N64ZXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}0${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"exten => _N64ZXXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}0${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"\n"+
	"; Format international number (add default prefix).\n"+
	"exten => _00.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _011.,1,Goto(<DEFAULT_PREFIX>00${EXTEN:3},1)\n"+
	"\n"+
	"; Dial an international number (if allowed).\n"+
	"exten => _N00.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _N0064.,1,Goto(${EXTEN:0:1}${EXTEN:3},1)\n"+
	"exten => _N011.,1,Goto(${EXTEN:0:1}00${EXTEN:4},1)\n"+
	dialplan_tail;


var dialplan_852 =
	";\n"+
	"; Dialplan Example for Hong Kong\n"+
	";--------------------------------\n"+
	"; Country code: 852\n"+
	"; Number length: 8-digits\n"+
	"; International Prefix: 001\n"+
	"; Trunk Prefix: none\n"+
	";\n"+
	dialplan_key +
	"; Format long distance number (add default prefix).\n"+
	"exten => _852NXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed)\n"+
	"exten => _NNXXXXXXX,1,Goto(${EXTEN:0:1}852${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (format correct)\n"+
	"exten => _N852NXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}${EXTEN:4},1:setcid${EXTEN},1)\n"+
	"\n"+
	"; Format international number (add default prefix).\n"+
	"exten => _001.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _00N.,1,Goto(<DEFAULT_PREFIX>001${EXTEN:3},1)\n"+
	"exten => _011.,1,Goto(<DEFAULT_PREFIX>001${EXTEN:3},1)\n"+
	"\n"+
	"; Dial an international number (if allowed).\n"+
	"exten => _N001.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _N001852.,1,Goto(${EXTEN:0:1}${EXTEN:4},1)\n"+
	"exten => _N00N.,1,Goto(${EXTEN:0:1}001${EXTEN:3},1)\n"+
	"exten => _N011.,1,Goto(${EXTEN:0:1}001${EXTEN:4},1)\n"+
	dialplan_tail;


var dialplan_853 =
	";\n"+
	"; Dialplan Example for Macau\n"+
	";--------------------------------\n"+
	"; Country code: 853\n"+
	"; Number length: 8-digits\n"+
	"; International Prefix: 00\n"+
	"; Trunk Prefix: none\n"+
	";\n"+
	dialplan_key +
	"; Format long distance number (add default prefix).\n"+
	"exten => _853NXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed)\n"+
	"exten => _NNXXXXXXX,1,Goto(${EXTEN:0:1}853${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (format correct)\n"+
	"exten => _N853NXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}${EXTEN:4},1:setcid${EXTEN},1)\n"+
	"\n"+
	"; Format international number (add default prefix).\n"+
	"exten => _00.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _01N.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _011.,1,Goto(<DEFAULT_PREFIX>00${EXTEN:3},1)\n"+
	"\n"+
	"; Dial an international number (if allowed).\n"+
	"exten => _N00.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _N01N.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _N00853.,1,Goto(${EXTEN:0:1}${EXTEN:3},1)\n"+
	"exten => _N011.,1,Goto(${EXTEN:0:1}00${EXTEN:4},1)\n"+
	dialplan_tail;


var dialplan_86 =
	";\n"+
	"; Dialplan Example for China\n"+
	";------------------------------\n"+
	"; Country code: 86\n"+
	"; Number length: 10 to 11-digits\n"+
	"; International Prefix: 00\n"+
	"; Trunk Prefix: 0\n"+
	";\n"+
	dialplan_key +
	"; Format long distance number (add default prefix, remove 0).\n"+
	"exten => _860ZXXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:0:2}${EXTEN:3},1)\n"+
	"exten => _860ZXXXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN:0:2}${EXTEN:3},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix).\n"+
	"exten => _86ZXXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _86ZXXXXXXXXXX,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"\n"+
	"; Format long distance number (add default prefix, remove 0).\n"+
	"exten => _0ZXXXXXXXXX,1,Goto(<DEFAULT_PREFIX>86${EXTEN:1},1)\n"+
	"exten => _0ZXXXXXXXXXX,1,Goto(<DEFAULT_PREFIX>86${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code)\n"+
	"exten => _NZXXXXXXXXX,1,Goto(${EXTEN:0:1}86${EXTEN:1},1)\n"+
	"exten => _NZXXXXXXXXXX,1,Goto(${EXTEN:0:1}86${EXTEN:1},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, add country code, remove 0)\n"+
	"exten => _N0ZXXXXXXXXX,1,Goto(${EXTEN:0:1}86${EXTEN:2},1)\n"+
	"exten => _N0ZXXXXXXXXXX,1,Goto(${EXTEN:0:1}86${EXTEN:2},1)\n"+
	"\n"+
	"; Format long distance number (prefix dialed, remove 0)\n"+
	"exten => _N860ZXXXXXXXXX,1,Goto(${EXTEN:0:1}${EXTEN:1:2}${EXTEN:4},1)\n"+
	"exten => _N860ZXXXXXXXXXX,1,Goto(${EXTEN:0:1}${EXTEN:1:2}${EXTEN:4},1)\n"+
	"\n"+
	"; Format long distance number (format correct)\n"+
	"exten => _N86ZXXXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}0${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"exten => _N86ZXXXXXXXXXX,1,GotoIf($[\"<STRIP_MSD>\" = \"Y\"]?setcid${EXTEN:0:1}0${EXTEN:3},1:setcid${EXTEN},1)\n"+
	"\n"+
	"; Format international number (add default prefix).\n"+
	"exten => _00.,1,Goto(<DEFAULT_PREFIX>${EXTEN},1)\n"+
	"exten => _011.,1,Goto(<DEFAULT_PREFIX>00${EXTEN:3},1)\n"+
	"\n"+
	"; Dial an international number (if allowed).\n"+
	"exten => _N00.,1,GotoIf($[\"<ALLOW_INTERNATIONAL>\" = \"Y\"]?setcid${EXTEN},1)\n"+
	"exten => _N0086.,1,Goto(${EXTEN:0:1}${EXTEN:3},1)\n"+
	"exten => _N011.,1,Goto(${EXTEN:0:1}00${EXTEN:4},1)\n"+
	dialplan_tail;


var carriers = Array(
    "Generic - SIP","genericSIP",
	"[genericSIP]\n"+
        "username=USER\n"+
        "secret=PASSWORD\n"+
        "host=HOST\n"+
        "type=friend\n"+
        "dtmfmode=auto\n"+
        "qualify=yes\n"+
        "trunk=yes\n"+
        "sendrpid=pai\n"+
        "trustrpid=yes\n",
            "",
                dialplan_1,
    "Generic - IAX2","genericIAX",
	"[genericIAX]\n"+
        "username=USER\n"+
        "secret=PASSWORD\n"+
        "host=HOST\n"+
        "type=friend\n"+
        "qualify=yes\n",
            "",
                dialplan_1,
    "Binfone - SIP","binfoneSIP",
	"[binfoneSIP]\n"+
        "username=USER\n"+
        "secret=PASSWORD\n"+
        "type=friend\n"+
        "host=sip.binfone.com\n"+
        "dtmfmode=auto\n"+
        "qualify=yes\n"+
        "trunk=yes\n"+
        "sendrpid=pai\n"+
        "trustrpid=yes\n",
            "USER:PASSWORD@sip.binfone.com/USER",
                dialplan_1,
    "Binfone - IAX2","binfoneIAX",
	"[binfoneIAX]\n"+
        "username=USER\n"+
        "secret=PASSWORD\n"+
        "type=friend\n"+
        "host=iax-2.binfone.com\n"+
        "qualify=yes\n",
            "USER:PASSWORD@iax-2.binfone.com",
                dialplan_1,
    "XcastLabs","xcast",
	"[xcast]\n"+
        "host=38.102.250.50\n"+
        "type=friend\n"+
        "dtmfmode=auto\n"+
        "qualify=yes\n"+
        "trunk=yes\n"+
        "sendrpid=pai\n"+
        "trustrpid=yes\n"+
	"\n"+
	"[xcastIN]\n"+
        "host=38.102.250.60\n"+
        "type=friend\n"+
        "dtmfmode=auto\n"+
        "qualify=yes\n"+
        "trunk=yes\n"+
        "sendrpid=pai\n"+
        "trustrpid=yes\n",
            "",
                dialplan_1,
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
                dialplan_1,
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
                dialplan_1,
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
                dialplan_1,
    "","", "", "", "",
    "-- Example Dialplans by Country --","", "", "", "",
    "Dialplan -   1 - North America", "", "", "", dialplan_1,
    "Dialplan -  44 - United Kingdom", "", "", "", dialplan_44,
    "Dialplan -  46 - Sweden", "", "", "", dialplan_46,
    "Dialplan -  48 - Poland", "", "", "", dialplan_48,
    "Dialplan -  61 - Australia", "", "", "", dialplan_61,
    "Dialplan -  64 - New Zealand", "", "", "", dialplan_64,
    "Dialplan -  86 - China", "", "", "", dialplan_86,
    "Dialplan - 852 - Hong Kong", "", "", "", dialplan_852,
    "Dialplan - 853 - Macau", "", "", "", dialplan_853
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
		if (opts[0] != '') {
			document.getElementById('carrier_description').value = sb.options[sb.selectedIndex].text;
			document.getElementById('carrier_name').value = opts[0];
			document.getElementById('carrier_protocol_config').value = 
				"; Do not add a 'context' directive.\n"+
				"; The name of your carrier MUST match the name in the first set of square-brackets '[]'.\n"+
				opts[1];
			document.getElementById('carrier_registrations').value = opts[2];
			if (sb.selectedIndex == 2 || sb.selectedIndex == 4) {
				document.getElementById('carrier_protocol').selectedIndex = 1;
			} else {
				document.getElementById('carrier_protocol').selectedIndex = 0;
			}
		}
		if (opts[3] != '') {
			document.getElementById('carrier_dialplan').value = opts[3];
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
